from flask import Flask, request, render_template, redirect, url_for, jsonify
from transformers.trainer_callback import TrainerControl, TrainerState
from transformers.training_args import TrainingArguments
from werkzeug.utils import secure_filename
import os
import json
from transformers import (
    AutoTokenizer,
    Trainer,
    TrainingArguments,
    AutoModelForSeq2SeqLM,
    TrainerCallback
)
import pandas as pd
import torch
from torch.utils.data import Dataset
from sklearn.model_selection import train_test_split
import uuid

# new imports for sub process
import subprocess
import threading
import pusher
import requests
from flask_cors import CORS



app = Flask(__name__)
CORS(app)
app.config["UPLOAD_FOLDER"] = "./uploads"
pusher_client = pusher.Pusher(
  app_id='1672928',
  key='c6345026f4a44535826a',
  secret='22229a6ee51f6b80e6e1',
  cluster='us3',
  ssl=True
)


class CustomDataset(Dataset):
    def __init__(self, prompts, completions, tokenizer):
        self.prompts = prompts
        self.completions = completions
        self.tokenizer = tokenizer

    def __len__(self):
        return len(self.prompts)

    def __getitem__(self, idx):
        input_encoding = self.tokenizer(
            self.prompts[idx],
            return_tensors="pt",
            padding="max_length",
            truncation=True,
            max_length=512,
        )
        target_encoding = self.tokenizer(
            self.completions[idx],
            return_tensors="pt",
            padding="max_length",
            truncation=True,
            max_length=512,
        )
        return {
            "input_ids": input_encoding["input_ids"].flatten(),
            "attention_mask": input_encoding["attention_mask"].flatten(),
            "labels": target_encoding["input_ids"].flatten(),
        }


class PusherCallback(TrainerCallback):
    def __init__(self, args, model_token, user_id):
        self.args = args
        self.model_token = model_token
        self.user_id = user_id

    # def on_epoch_end(self, args, state, control, **kwargs):
    #     metrics = state.log_history
    #     metrics_string = json.dumps(metrics)

    #     pusher_message_json = {
    #         "epoch": state.epoch,
    #         "batch_size": args.per_device_train_batch_size,
    #         "learning_rate": args.learning_rate,
    #         "model_token": self.model_token,
    #         "status": "epoch end",
    #         "metrics": state.log_history,
    #     }
    #     pusher_client.trigger(self.user_id, 'ai_model', {'message': pusher_message_json})
    #     data_status_request = {
    #         "status": "trained epoch " + str(state.epoch) + " of " + str(args.num_train_epochs),
    #         "metrics": metrics_string,
    #     }
    #     req = requests.post(
    #         'http://localhost:8000/api/v1/models/' + self.model_token + '/status',
    #         data=data_status_request
    #     )
    #     print(req.text)

    def on_train_begin(self, args, state, control, **kwargs):
        data_status_request = {
            "status": "training begin",
        }
        req = requests.post(
            'http://localhost:8000/api/v1/models/' + self.model_token + '/status',
            data=data_status_request
        )
        print(req.text)

    def on_log(self, args, state, control, **kwargs):
        metrics = state.log_history
        metrics_string = json.dumps(metrics)

        pusher_message_json = {
            "epoch": state.epoch,
            "batch_size": args.per_device_train_batch_size,
            "learning_rate": args.learning_rate,
            "model_token": self.model_token,
            "status": "epoch end",
            "metrics": state.log_history,
        }
        pusher_client.trigger(self.user_id, 'ai_model', {'message': pusher_message_json})
        data_status_request = {
            "status": "trained epoch " + str(state.epoch) + " of " + str(args.num_train_epochs),
            "metrics": metrics_string,
        }
        req = requests.post(
            'http://localhost:8000/api/v1/models/' + self.model_token + '/status',
            data=data_status_request
        )
        print(req.text)

    def on_train_end(self, args, state, control, **kwargs):
        data_status_request = {
            "status": "trained",
        }
        req = requests.post(
            'http://localhost:8000/api/v1/models/' + self.model_token + '/status',
            data=data_status_request
        )

        pusher_message_json = {
            "epoch": state.epoch,
            "batch_size": args.per_device_train_batch_size,
            "learning_rate": args.learning_rate,
            "model_token": self.model_token,
            "status": "training end",
            "metrics": state.log_history,
        }
        pusher_client.trigger(self.user_id, 'ai_model', {'message': pusher_message_json})









def train(file, epoch, batch_size, learning_rate, random_filename,user_id):
    # Load the SQLcoder tokenizer and model
    train_base_checkpoint = "t5-small"
    device = "cuda"  # for GPU usage or "cpu" for CPU usage

    train_tokenizer = AutoTokenizer.from_pretrained(train_base_checkpoint)
    train_model = AutoModelForSeq2SeqLM.from_pretrained(train_base_checkpoint).to(device)

    # Read the training data
    data = pd.read_csv(file)

    # Split your data into training and evaluation sets
    train_data, eval_data = train_test_split(data, test_size=0.2, random_state=42)

    # Create the training and evaluation datasets
    train_dataset = CustomDataset(
        train_data["prompt"].tolist(),
        train_data["completion"].tolist(),
        train_tokenizer,
    )

    eval_dataset = CustomDataset(
        eval_data["prompt"].tolist(),
        eval_data["completion"].tolist(),
        train_tokenizer,
    )

    # Training arguments
    training_args = TrainingArguments(
        output_dir="./output",
        num_train_epochs=epoch,  # Increasing to 100 based on size of dataset
        per_device_train_batch_size=batch_size,  # Experiment with this, depends on your GPU memory
        per_device_eval_batch_size=batch_size,  # Similarly this can be experimented with
        learning_rate=learning_rate,  # setting learning rate
        weight_decay=0.01,  # weight decay for regularization
        logging_dir="./logs",
        logging_steps=10,
        save_strategy="epoch",
        evaluation_strategy="epoch",
        save_total_limit=2,
        gradient_accumulation_steps=1,  # Optional - can be used if experiencing memory issues
        remove_unused_columns=False,
        push_to_hub=False,
    )

    # Create a list of callbacks
    callbacks = [PusherCallback(args=training_args, model_token=random_filename, user_id=user_id)]

    # Create Trainer with both training and evaluation datasets
    trainer = Trainer(
        model=train_model,
        args=training_args,
        train_dataset=train_dataset,
        eval_dataset=eval_dataset,  # Provide the evaluation dataset
        callbacks=callbacks,

    )

    # Fine-tune the model
    trainer.train()

    # Save the model
    path = "./models/" + random_filename  # hard-code output path
    model_path = path + "/model"
    tokenizer_path = path + "/tokenizer"

    train_model.save_pretrained(model_path)
    train_tokenizer.save_pretrained(tokenizer_path)



@app.route("/", methods=["GET", "POST"])
def index():
    res_dict = {"message": "Internal interface for training and serving the model"}
    return jsonify(res_dict)


@app.route("/api/v1/train", methods=["POST"])
def train_model():
    # if windows
    # file = "..\\..\\storage\\app\\public\\"+request.form.get("file")
    # if linux
    # file = "../../storage/app/public/"+request.form.get("file")

    if os.name == "nt":
        file = "..\\storage\\app\\" + request.form.get("file")
    else:
        file = "../storage/app/" + request.form.get("file")
    # convert from path to file
    # file name its last part of path
    filename = file.split("/")[-1]

    if file:
        random_filename = str(uuid.uuid4())

        epoch = int(request.form.get("epoch"))
        batch_size = int(request.form.get("batch_size"))
        learning_rate = float(request.form.get("learning_rate"))
        user_id = request.form.get("user_id")

        # train(
        #     os.path.join(app.config["UPLOAD_FOLDER"], filename),
        #     epoch,
        #     batch_size,
        #     learning_rate,
        #     random_filename,
        # )

        # new code for sub process
        # create a thread to run the training
        thread = threading.Thread(
            target=train,
            args=(
                file,
                epoch,
                batch_size,
                learning_rate,
                random_filename,
                user_id,
            ),
        )
        thread.start()



        res_dict = {
            "epoch": epoch,
            "batch_size": batch_size,
            "learning_rate": learning_rate,
            "file": filename,
            "model_token": random_filename,
            "status": "success",
        }
        return jsonify(res_dict)
    else:
        return jsonify({"status": "failed", "message": "no file uploaded"})


@app.route("/api/v1/prompt", methods=["GET", "POST"])
def prompt():
    # method options retuern cors headers
    if request.method == "OPTIONS":
        return jsonify({"status": "success"})

    # model path
    model_base_path = request.form.get("model_token")
    model_base_path = "./models/" + model_base_path
    model_path = model_base_path + "/model"
    tokenizer_path = model_base_path + "/tokenizer"

    max_length = int(request.form.get("max_tokens"))

    temperature = float(request.form.get("temperature"))

    print(temperature, max_length)

    # if model not found
    if not os.path.exists(model_path):
        res_dict = {
            "model_token": model_base_path,
            "status": "failed",
            "message": "model not found",
        }
        return jsonify(res_dict)

    tokenizer = AutoTokenizer.from_pretrained(tokenizer_path)
    model = AutoModelForSeq2SeqLM.from_pretrained(model_path)

    prompt = request.form.get("prompt")
    output = model.generate(**tokenizer(prompt, return_tensors="pt"), max_length=max_length, temperature=temperature)
    completion = tokenizer.decode(output[0], skip_special_tokens=True, clean_up_tokenization_spaces=True)
    # return json response
    res_dict = {"prompt": prompt, "completion": completion}
    return jsonify(res_dict)



@app.route("/api/v1/model/<model_token>", methods=["DELETE"])
def delete_model(model_token):
    model_token = "./models/" + model_token
    # if model not found
    if not os.path.exists(model_token):
        res_dict = {
            "model_token": model_token,
            "status": "failed",
            "message": "model not found",
        }
        return jsonify(res_dict)

    model_path = model_token + "/model"
    tokenizer_path = model_token + "/tokenizer"
    os.remove(model_path)
    os.remove(tokenizer_path)
    os.remove(model_token)
    res_dict = {"model_token": model_token, "status": "deleted"}
    return jsonify(res_dict)


if __name__ == "__main__":
    app.run(debug=True)

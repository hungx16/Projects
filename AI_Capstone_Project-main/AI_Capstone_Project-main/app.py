import gradio
import cv2
import numpy as np
import face_recognition
import tensorflow as tf
from PIL import Image, ImageTk
from keras.models import model_from_json

emotions = {0: "Angry", 1: "Disgusted", 2: "Fearful", 3: "Happy", 4: "Neutral", 5: "Sad", 6: "Surprised"}

# load json and create model
json_file = open('model/emotion_model.json', 'r')
loaded_model_json = json_file.read()
json_file.close()
model = model_from_json(loaded_model_json)

# load weights into new model
model.load_weights("model/emotion_model.h5")

def processImage(img):
    face_locations = face_recognition.face_locations(img)
    t,r,b,l = face_locations[0]
    face_img = img[t:b, l:r]
    pil_img = Image.fromarray(face_img)
    cv_img = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)
    gray_img = cv2.cvtColor(cv_img, cv2.COLOR_BGR2GRAY)
    final_img = np.expand_dims(np.expand_dims(cv2.resize(gray_img, (48, 48)), -1), 0)
    return final_img

def getScore(input):
    input_img = processImage(input)
    prediction = model.predict(input_img)
    idx = int(np.argmax(prediction))
    return emotions[idx]

demo = gradio.Interface(getScore, gradio.Image(shape=(200,200)), "text")
demo.launch(share=True)


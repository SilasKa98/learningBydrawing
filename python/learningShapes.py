import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping

import numpy as np
import pandas as pd
import random
import os
import matplotlib.pyplot as plt
from keras.preprocessing.image import load_img, img_to_array
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, log_loss, accuracy_score
from keras.preprocessing.image import ImageDataGenerator

train_dir = 'learningData/six-shapes-dataset-v1/six-shapes/train'
test_dir = 'learningData/six-shapes-dataset-v1/six-shapes/test'


Name=[]
for file in os.listdir(train_dir):
    Name+=[file]
print(Name)
print(len(Name))
num_classes = len(Name)

N=list(range(len(Name)))
mapping=dict(zip(Name,N))
reverse_mapping=dict(zip(N,Name))


trainset=[]
count=0
for file in os.listdir(train_dir):
    path=os.path.join(train_dir,file)
    for im in os.listdir(path):
        image=load_img(os.path.join(path,im), color_mode="grayscale", target_size=(28,28,1))
        image=img_to_array(image)
        image=image / 255.0
        trainset+=[[image,count]]
    count=count+1

testset = []
count = 0
for file in os.listdir(test_dir):
    path = os.path.join(test_dir, file)
    for im in os.listdir(path):
        image = load_img(os.path.join(path, im), color_mode="grayscale", target_size=(28,28,1))
        image = img_to_array(image)
        image = image / 255.0
        testset += [[image, count]]
    count = count + 1


trainX,trainY0=zip(*trainset)
testX,testY0=zip(*testset)

labels1=keras.utils.to_categorical(trainY0)
trainY=np.array(labels1)

trainX=np.array(trainX)
testX=np.array(testX)

print(trainX)
print(trainX.shape)
print(trainX.shape[0])

trainx,testx,trainy,testy=train_test_split(trainX,trainY,test_size=0.1,random_state=44)


print(trainx.shape)
print(testx.shape)
print(trainy.shape)
print(testy.shape)

print("done")

datagen = ImageDataGenerator(horizontal_flip=True,vertical_flip=True,rotation_range=20,zoom_range=0.2,
                        width_shift_range=0.2,height_shift_range=0.2,shear_range=0.1,fill_mode="nearest")

early_stopping = EarlyStopping(monitor='val_loss', patience=3)

model = tf.keras.Sequential()

model.add(tf.keras.layers.Conv2D(filters=128, kernel_size=(5,5), padding = 'same', activation='relu',input_shape=(28,28,1)))
model.add(tf.keras.layers.MaxPooling2D(pool_size=(2,2), strides=(2,2)))
model.add(tf.keras.layers.Conv2D(filters=64, kernel_size=(3,3) , padding = 'same', activation='relu'))
model.add(tf.keras.layers.MaxPooling2D(pool_size=(2,2)))
model.add(tf.keras.layers.Flatten())
model.add(tf.keras.layers.Dense(units=128, activation='relu'))
model.add(tf.keras.layers.Dropout(.5))
model.add(tf.keras.layers.Dense(units=num_classes, activation='softmax'))

model.summary()

model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])

'''
model.fit(
    datagen.flow(trainx,trainy,batch_size=8),
    validation_data=(testx,testy),
    verbose=1, # Suppress chatty output; use Tensorboard instead
    epochs=30,
    callbacks=[early_stopping]
)

'''
model.fit(
    trainx,
    trainy,
    batch_size=8,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    validation_data=(testx, testy),
    epochs=30,
    callbacks=[early_stopping]
)


test_loss,test_acc = model.evaluate(testx, testy)
print('Test accuracy:', test_acc)
model.summary()


tfjs.converters.save_keras_model(model, 'saved_models/formen')



pred2=model.predict(testX)
print(pred2.shape)

PRED=[]
for item in pred2:
    value2=np.argmax(item)
    PRED+=[value2]

ANS = testY0

accuracy=accuracy_score(ANS,PRED)
print(accuracy)

N=list(range(len(testX)))
random.seed(2021)
random.shuffle(N)

fig, axs = plt.subplots(3,3,figsize=(12,12))
for i in range(9):
    r=i//3
    c=i%3
    ax=axs[r][c].axis("off")
    actual=reverse_mapping[testY0[N[i]]]
    predict=reverse_mapping[PRED[N[i]]]
    ax=axs[r][c].set_title(actual+'=='+predict)
    ax=axs[r][c].imshow(testX[N[i]])
plt.show()
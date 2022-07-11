import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras

from sklearn.datasets import load_digits
#import matplotlib.pyplot as plt
import numpy as np
from sklearn.model_selection import train_test_split
from datetime import datetime
from keras.models import Sequential
from keras.layers import Dense
import matplotlib.pyplot as plt
from keras import models
#from keras.utils import  plot_model
import pickle
from keras.datasets import mnist
import numpy as np
from keras.layers import Conv2D, MaxPool2D, Dense, GlobalAveragePooling2D

# split the mnist data into train and test
(train_img, train_label), (test_img, test_label) = keras.datasets.mnist.load_data()

# reshape and scale the data
train_img = train_img.reshape([-1, 28, 28, 1])
test_img = test_img.reshape([-1, 28, 28, 1])
train_img = train_img / 255.0
test_img = test_img / 255.0

# convert class vectors to binary class matrices --> one-hot encoding
train_label = keras.utils.to_categorical(train_label)
test_label = keras.utils.to_categorical(test_label)

classes = np.shape(train_label)[1]
num_epochs = 20
model = Sequential()
# First convolutional and pooling layer
model.add(Conv2D(input_shape=(28, 28,1), filters=64, kernel_size=3, padding='valid', activation='relu',kernel_initializer='he_uniform'))
model.add(MaxPool2D(strides=2, pool_size=2))
model.add(Conv2D(filters=64, kernel_size=3, padding='valid', activation='relu'))

model.add(GlobalAveragePooling2D())
# FCC
model.add(Dense(units=16, activation='relu'))

# Classifier
model.add(Dense(units=classes, activation='softmax'))

# Compile model
model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])



# Train model
training_history = model.fit(
    train_img, # input
    train_label, # output
    batch_size=16,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    epochs=num_epochs,
    validation_data=(test_img,test_label)
)

test_loss,test_acc = model.evaluate(test_img, test_label)
print('Test accuracy:', test_acc)


tfjs.converters.save_keras_model(model, 'saved_models')
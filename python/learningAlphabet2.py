import tensorflow as tf
import tensorflowjs as tfjs
import pandas as pd
import numpy as np
from tensorflow import keras
from keras.callbacks import EarlyStopping
from sklearn.model_selection import train_test_split


test = pd.read_csv('learningData/emnist-letters-test.csv')
train = pd.read_csv('learningData/emnist-letters-train.csv')

print('training dataset dimensions: ', train.shape)
print('test dataset dimensions: ', test.shape, '\n')

# view the head of the training dataset
test.head()

# update column names for both datasets
columns = ['labels']
for i in range(train.shape[1] - 1):
    columns.append(i)

train.columns = columns
test.columns = columns

classes = train['labels'].unique()
print('number of classes: ', len(classes))

train.head()


from sklearn.model_selection import train_test_split

# split training and validation data using sklearn
x_train, x_val, y_train, y_val = train_test_split(train.drop(['labels'], axis=1),
                                                  train.labels - 1,
                                                  train_size=0.8,
                                                  test_size=0.2,
                                                  random_state=42)

# reshape and normalize test data
x_train = x_train / 255.0
x_val = x_val / 255.0

testX = test.values[:, 1:].reshape(test.shape[0],28, 28, 1).astype('float32')
x_test = testX / 255.0
y_test = test['labels'].values - 1 # this is just to make the neurons in the output layer start at 0

print('trianing set: ', x_train.shape, y_train.shape)
print('validation set: ', x_val.shape, y_val.shape)
print('test set: ', x_test.shape, y_test.shape)

import matplotlib.pyplot as plt
import random

# select random samples from training dataset
train_samples = random.sample(range(0, len(x_train)), 9)
test_samples = random.sample(range(0, len(x_val)), 9)

x_train
plt.figure(figsize=(6, 6))
plt.suptitle('Training set')
for i in train_samples:
    plt.subplot(3, 3, train_samples.index(i) + 1)
    plt.imshow(x_train.iloc[i, :].values.reshape(28, 28), cmap='binary')
    plt.title(f'label: {y_train.iloc[i]}')
    plt.axis('off')

plt.figure(figsize=(6, 6))
plt.suptitle('Test set')
for i in test_samples:
    plt.subplot(3, 3, test_samples.index(i) + 1)
    plt.imshow(x_val.iloc[i, :].values.reshape(28, 28), cmap='binary')
    plt.title(f'label: {y_val.iloc[i]}')
    plt.axis('off')

import tensorflow as tf

# set accuracy
desired_accuracy = 0.92


# create callback to stop training when we reached desired accuracy
class myCallback(tf.keras.callbacks.Callback):
    def on_epoch_end(self, epoch, logs={}):
        if (logs.get('accuracy') is not None and logs.get('accuracy') >= desired_accuracy):
            print('\nReached 92% training accuracy: cancelling training...')
            self.model.stop_training = True


# instantiate callback
callbacks = myCallback()


def train_model():
    # define model
    model = tf.keras.models.Sequential([
        # initial normalization
        tf.keras.layers.Reshape((28, 28, 1), input_shape=(784,)),
        #         tf.keras.layers.BatchNormalization(),

        # first convolution
        tf.keras.layers.Conv2D(8, (3, 3), activation='relu'),  # applies kernels to our data
        tf.keras.layers.MaxPooling2D(2, 2),  # reduce dimension
        #         tf.keras.layers.BatchNormalization(),
        #         tf.keras.layers.Dropout(0.4),

        # second convolution
        tf.keras.layers.Conv2D(16, (3, 3), activation='relu'),
        tf.keras.layers.MaxPooling2D(2, 2),
        #         tf.keras.layers.BatchNormalization(),
        #         tf.keras.layers.Dropout(0.4),

        # third convolution
        tf.keras.layers.Conv2D(24, (3, 3), activation='relu'),
        #         tf.keras.layers.MaxPooling2D(2, 2),
        #         tf.keras.layers.BatchNormalization(),
        #         tf.keras.layers.Dropout(0.4),

        # feed to DNN
        tf.keras.layers.Flatten(),
        tf.keras.layers.Dense(128, activation='relu'),
        #         tf.keras.layers.Dropout(0.2),
        tf.keras.layers.Dense(len(classes), activation=tf.nn.softmax)  # generalized logistic regression
    ])

    # use sparse categorical crossentropy since values are labeled from 0-25
    model.compile(optimizer='adam', loss='sparse_categorical_crossentropy',
                  metrics=['accuracy'])

    return model


# view model summary before running neural network
model = train_model()
model.summary()


# train neural network and have it automatically stop on 95% accuracy
history = model.fit(x_train, y_train, epochs=200,
                    validation_data=(x_val, y_val),
                    batch_size=4096, verbose=1,
                    callbacks=[callbacks])


tfjs.converters.save_keras_model(model, 'saved_models/Buchstaben')
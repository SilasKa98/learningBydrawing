import datetime

import tensorflow as tf
import tensorflowjs as tfjs
import pandas as pd
import os
import numpy as np

from keras.layers import Conv2D, BatchNormalization, Dropout, MaxPooling2D, Flatten, Dense
from keras.models import Sequential
from tensorflow import keras
from keras.callbacks import EarlyStopping, ReduceLROnPlateau
from sklearn.model_selection import train_test_split
from keras.optimizer_v2.rmsprop import RMSprop

# -------------------------------------------------------------------------------------------------------------------- #
#                                             Variables / Data paths                                                   #
# -------------------------------------------------------------------------------------------------------------------- #
# Defining Paths to the Learning, Test, Validation Data used
os.listdir('learningData/kuzushiji')
train_set = np.load('learningData/kuzushiji/kmnist-train-imgs.npz')['arr_0']
train_labels = np.load('learningData/kuzushiji/kmnist-train-labels.npz')['arr_0']
test_set = np.load('learningData/kuzushiji/kmnist-test-imgs.npz')['arr_0']
test_labels = np.load('learningData/kuzushiji/kmnist-test-labels.npz')['arr_0']


# -------------------------------------------------------------------------------------------------------------------- #
#                                                   Preparation                                                        #
# -------------------------------------------------------------------------------------------------------------------- #
def data_preparation():
    """
    Function which reads the data and creates some important variables
    """
    num_class = len(np.unique(train_labels))

    print(num_class)
    char_df = pd.read_csv('learningData/kuzushiji/kmnist_classmap.csv', encoding = 'utf-8')
    print(char_df)

    train_x, train_y = data_preprocessing(train_set, train_labels, num_class)
    test_x, test_y = data_preprocessing(test_set, test_labels, num_class)

    train_x, val_x, train_y, val_y = train_test_split(train_x, train_y, test_size=0.2, random_state=0)

    print("KMNIST train -  rows:", train_x.shape[0], " columns:", train_x.shape[1:4])
    print("KMNIST valid -  rows:", val_x.shape[0], " columns:", val_x.shape[1:4])
    print("KMNIST test -  rows:", test_x.shape[0], " columns:", test_x.shape[1:4])

    return train_x, train_y, test_x, test_y, val_x, val_y, num_class


def data_preprocessing(images, labels, num_class):
    """
    Function to create one hot encoding for the data and reshapes the
    images to a 28 by 28 image array
    """
    out_y = keras.utils.to_categorical(labels, num_class)
    num_images = images.shape[0]
    x_shaped_array = images.reshape(num_images, 28, 28, 1)
    out_x = x_shaped_array / 255

    return out_x, out_y


def model_definition(num_class, dropout_layer):
    """
    Function to initialise the model used for training
    with all the layer definitions
    """
    model = Sequential()

    model.add(Conv2D(128, kernel_size=(5, 5), activation='relu', padding="same",
                     kernel_initializer='he_normal', input_shape=(28, 28, 1)))
    model.add(BatchNormalization())

    model.add(Conv2D(64, kernel_size=(5, 5), activation='relu'))
    model.add(BatchNormalization())
    model.add(Conv2D(32, kernel_size=5, strides=2, padding='same', activation='relu'))
    model.add(MaxPooling2D((2, 2)))
    model.add(BatchNormalization())
    model.add(Dropout(dropout_layer))

    model.add(Conv2D(64, kernel_size=(5, 5), strides=2, padding='same', activation='relu'))
    model.add(MaxPooling2D(pool_size=(2, 2)))
    model.add(BatchNormalization())
    model.add(Conv2D(32, kernel_size=(3, 3), strides=2, padding='same', activation='relu'))
    model.add(Dropout(dropout_layer))

    model.add(Flatten())
    model.add(Dense(200, activation='relu'))
    model.add(Dropout(dropout_layer))
    model.add(Dense(num_class, activation='softmax'))

    optimizer = RMSprop(learning_rate=0.001, rho=0.9, epsilon=1e-08, decay=0.0)
    model.compile(loss="categorical_crossentropy", optimizer=optimizer, metrics=["accuracy"])
    model.summary()

    return model


def model_training(model, train_x, train_y, batch_size, epoch, val_x, val_y, patience):
    """
    Function to train the defined model with created train and val data
    """
    # stopping mechanism to save the best model after x rounds with no improvement
    early_stopping = EarlyStopping(monitor='val_loss', patience=patience)

    # Save logs of the trained model to be able to evaluate the model with tensorboard
    logdir = "logs/scalars/" + datetime.now().strftime("%Y%m%d-%H%M%S")
    tensorboard_callback = keras.callbacks.TensorBoard(log_dir=logdir, write_graph=True)

    # changes / reduces the Learning rate if no improvements are recognizable
    learning_rate_reduction = ReduceLROnPlateau(monitor='val_accuracy',
                                                patience=3,
                                                verbose=1,
                                                factor=0.5,
                                                min_lr=0.00001)

    # trains the model with the given data and Hyper-parameters
    model.fit(
        train_x,
        train_y,
        batch_size=1024,
        epochs=100,
        verbose=1,
        validation_data=(val_x, val_y),
        callbacks=[early_stopping, learning_rate_reduction,tensorboard_callback]
        )

    return model


def model_evaluation(model, test_x, test_y):
    """
    Function to evaluate the model after training with the test data (model never seen this data)
    prints accuracy and loss of the pass
    """
    test_loss, test_acc = model.evaluate(test_x, test_y)

    print('Test accuracy:', test_acc)
    print("Test loss: ", test_loss)


def model_save(model, save_status):
    """
    Function to save the Model for JavaScript Use if save_status True
    """
    if save_status:
        tfjs.converters.save_keras_model(model, 'saved_models/hiragana')
        print("Model Saved")
    else:
        print("Model not Saved")


# -------------------------------------------------------------------------------------------------------------------- #
#                                           Hyper-parameters & Variables                                               #
# -------------------------------------------------------------------------------------------------------------------- #
save = True
stopping_patience = 8

batch_size = 1024
epoch = 100
dropout = 0.4

# -------------------------------------------------------------------------------------------------------------------- #
#                                                      Execution                                                       #
# -------------------------------------------------------------------------------------------------------------------- #
train_x, train_y, test_x, test_y, val_x, val_y, num_class = data_preparation()
model = model_definition(num_class, dropout)
trained_model = model_training(model, train_x, train_y, batch_size, epoch, val_x, val_y, stopping_patience)
model_evaluation(trained_model, test_x, test_y)
model_save(trained_model, save)


"""
model = tf.keras.Sequential()

model.add(tf.keras.layers.Conv2D(32,(5,5),activation = 'relu', input_shape = (28,28,1), padding="same"))
model.add(tf.keras.layers.MaxPooling2D(2,2))
model.add(tf.keras.layers.BatchNormalization())

model.add(tf.keras.layers.Conv2D(64,(5,5),activation = 'relu'))

model.add(tf.keras.layers.Conv2D(64,(5,5),activation = 'relu'))
model.add(tf.keras.layers.Flatten())

model.add(tf.keras.layers.Dense(64,activation = 'relu'))
model.add(tf.keras.layers.Dropout(0.4))

model.add(tf.keras.layers.Dense(num_class,activation = 'softmax'))

model.summary()
model.compile(loss = 'categorical_crossentropy',
              optimizer = 'adam',
              metrics = ['accuracy'])


history = model.fit(
    train_set,train_labels,
    epochs=40,
    batch_size=batch_size,
    verbose = 1,
    validation_data = (validation_set,validation_labels),
    callbacks=[early_stopping]
)"""
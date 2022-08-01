import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping
from datetime import datetime

import numpy as np
import random
import os
import matplotlib.pyplot as plt
from keras.preprocessing.image import load_img, img_to_array
from sklearn.metrics import ConfusionMatrixDisplay, confusion_matrix
from keras.preprocessing.image import ImageDataGenerator


# -------------------------------------------------------------------------------------------------------------------- #
#                                             Variables / Data paths                                                   #
# -------------------------------------------------------------------------------------------------------------------- #
# Defining Paths to the Learning, Test, Validation Data used
train_dir = 'learningData/six-shapes-dataset-v1-inverted/six-shapes/train'
test_dir = 'learningData/six-shapes-dataset-v1-inverted/six-shapes/test'
val_dir = 'learningData/six-shapes-dataset-v1-inverted/six-shapes/val'


# -------------------------------------------------------------------------------------------------------------------- #
#                                                   Preparation                                                        #
# -------------------------------------------------------------------------------------------------------------------- #
def data_analysing():
    """
    Function which reads the data and creates some important variables
    """
    classes = []                                            # List of unique classes
    for file in os.listdir(train_dir):
        classes.append(file)

    num_classes = len(classes)                              # Number of unique classes
    class_numbers = list(range(len(classes)))               # List of unique classes as numbers for mapping
    mapping = dict(zip(classes, class_numbers))             # mapping of the classes --> numbers
    reverse_mapping = dict(zip(class_numbers, classes))     # mapping of numbers --> classes for reversing

    # Some information outputs about the data
    print("Unique Classes: ", classes)
    print("Nuber of unique classes: ", num_classes)
    print("Classes to Number Mapping: ", mapping)
    print("Number to Classes Mapping: ", reverse_mapping)

    return classes, num_classes, class_numbers, mapping, reverse_mapping


def data_preparation():
    """
    Function to create the test, training and validation datasets
    and transformation of the sets to shape which can be used by tensorflow
    """
    # Create all data_sets for Training, Testing and Validation
    train_set = create_data_set(train_dir)
    test_set = create_data_set(test_dir)
    val_set = create_data_set(val_dir)

    train_x, train_y = zip(*train_set)
    test_x, test_y = zip(*test_set)
    val_x, val_y = zip(*val_set)
    test_y0 = test_y

    # Create one hot encoding for train data (to be used in the Neural Network)
    labels_train = keras.utils.to_categorical(train_y)
    train_y = np.array(labels_train)
    labels_val = keras.utils.to_categorical(val_y)
    val_y = np.array(labels_val)
    labels_test = keras.utils.to_categorical(test_y)
    test_y = np.array(labels_test)

    # Create Array for Feature Data (to be used in Neural Network)
    train_x = np.array(train_x)
    test_x = np.array(test_x)
    val_x = np.array(val_x)

    # Information Prints for the Data Shape (number of records etc.)
    print("Shape of training data: ", train_x.shape)
    print("Shape of test data: ", test_x.shape)
    print("Shape of validation data: ", val_x.shape)

    return train_x, test_x, val_x, train_y, test_y, val_y, test_y0


def create_data_set(data_dir):
    """
    Function create the respective data sets for the paths of the image folders
    Images are saved as array to be used in tensorflow
    """
    data_set = []
    count = 0
    for file in os.listdir(data_dir):
        path = os.path.join(data_dir, file)
        for img in os.listdir(path):
            image = load_img(os.path.join(path, img), color_mode="grayscale", target_size=(28, 28, 1))
            image = img_to_array(image)     # image to array conversion
            image = image / 255             # normalization of the value between 0 and 1 for better performance
            data_set += [[image, count]]    # add image to set with count(number of images added for the respective set)
        count = count+1

    return data_set


# -------------------------------------------------------------------------------------------------------------------- #
#                                          Model Definition, Training & Evaluation                                     #
# -------------------------------------------------------------------------------------------------------------------- #
def model_definition(num_classes,dropout_layer):
    """
    Function to initialise the model used for training
    with all the layer definitions
    """
    model_def = tf.keras.Sequential()

    # Defining the Layers of the Neural Network
    model_def.add(tf.keras.layers.Conv2D(filters=128, kernel_size=(5, 5), padding='same', activation='relu',
                                         input_shape=(28, 28, 1)))
    model_def.add(tf.keras.layers.Dropout(dropout_layer))
    model_def.add(tf.keras.layers.MaxPooling2D(pool_size=(2, 2), strides=(2, 2)))
    model_def.add(tf.keras.layers.Conv2D(filters=64, kernel_size=(3, 3), padding='same', activation='relu'))
    model_def.add(tf.keras.layers.Dropout(dropout_layer))
    model_def.add(tf.keras.layers.MaxPooling2D(pool_size=(2, 2)))
    model_def.add(tf.keras.layers.Flatten())
    model_def.add(tf.keras.layers.Dense(units=128, activation='relu'))
    model_def.add(tf.keras.layers.Dropout(dropout_layer))
    model_def.add(tf.keras.layers.Dense(units=num_classes, activation='softmax'))
    model_def.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])

    model_def.summary()

    return model_def


def model_training(model, train_x, train_y, val_x, val_y, batch, epochs, patience, rotation):
    """
    Function to train the defined model with created train and val data
    """
    # rotates / zooms the image to create more variety and training data
    datagen = ImageDataGenerator(horizontal_flip=True, vertical_flip=True, rotation_range=20, zoom_range=0.2,
                                 width_shift_range=0.2, height_shift_range=0.2, shear_range=0.1, fill_mode="nearest")
    # stopping mechanism to save the best model after x rounds with no improvement
    early_stopping = EarlyStopping(monitor='val_loss', patience=patience)

    # Save logs of the trained model to be able to evaluate the model with tensorboard
    logdir = "logs/scalars/" + datetime.now().strftime("%Y%m%d-%H%M%S")
    tensorboard_callback = keras.callbacks.TensorBoard(log_dir=logdir, write_graph=True)

    # checks if the rotations is enabled or not and uses the respective training method
    if rotation:
        model.fit(
            datagen.flow(
                train_x,
                train_y,
                batch_size=batch
            ),
            verbose=1,  # Suppress chatty output; use Tensorboard instead
            validation_data=(val_x, val_y),     # Data to be used as Validation for the training Process
            epochs=epochs,
            callbacks=[early_stopping,tensorboard_callback]
        )
    else:
        model.fit(
            train_x,
            train_y,
            batch_size=batch,
            verbose=1,  # Suppress chatty output; use Tensorboard instead
            validation_data=(val_x, val_y),  # Data to be used as Validation for the training Process
            epochs=epochs,
            callbacks=[early_stopping]
        )

    return model


def model_evaluation(model, test_x, test_y, test_y0, classes):
    """
    Function to evaluate the model after training with the test data (model never seen this data)
    plots 9 images plus predictions
    plots a confusion matrix of all predictions to see how much are wrong and for which classes
    """
    test_loss, test_acc = model.evaluate(test_x, test_y)

    print('Test accuracy:', test_acc)
    print("Test loss: ", test_loss)

    answers = test_y0
    pred2 = model.predict(test_x)
    prediction = []

    for item in pred2:
        value2 = np.argmax(item)
        prediction += [value2]

    N = list(range(len(test_x)))
    random.seed(2021)
    random.shuffle(N)

    fig, axs = plt.subplots(3, 3, figsize=(12, 12))
    for i in range(9):
        r = i // 3
        c = i % 3
        ax = axs[r][c].axis("off")
        actual = reverse_mapping[test_y0[N[i]]]
        predict = reverse_mapping[prediction[N[i]]]
        ax = axs[r][c].set_title(actual + '==' + predict)
        ax = axs[r][c].imshow(test_x[N[i]])
    plt.show()

    cm = confusion_matrix(answers, prediction)
    disp = ConfusionMatrixDisplay(confusion_matrix=cm, display_labels=classes)
    disp.plot()
    plt.show()


def model_save(trained_model, save_status):
    """
    Function to save the Model for JavaScript Use if save_status True
    """
    if save_status:
        tfjs.converters.save_keras_model(trained_model, 'saved_models/formen_test')
    else:
        print("Model not Saved")


# -------------------------------------------------------------------------------------------------------------------- #
#                                           Hyper-parameters & Variables                                               #
# -------------------------------------------------------------------------------------------------------------------- #
save = False
stopping_patience = 5
use_rotation = True         # Rotates the images in a specified angle for training

batch_size = 128
epoch = 100
dropout = 0.2


# -------------------------------------------------------------------------------------------------------------------- #
#                                                      Execution                                                       #
# -------------------------------------------------------------------------------------------------------------------- #
classes, num_classes, class_numbers, mapping, reverse_mapping = data_analysing()
train_x, test_x, val_x, train_y, test_y, val_y, test_y0 = data_preparation()
model = model_definition(num_classes, dropout)
trained_model = model_training(model, train_x, train_y, val_x, val_y,
                               batch_size, epoch, stopping_patience, use_rotation)
model_evaluation(model, test_x, test_y, test_y0, classes)
model_save(trained_model, save)
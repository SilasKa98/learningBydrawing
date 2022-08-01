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



from tensorflow import keras
from keras import Sequential
import numpy as np
from keras.layers import Conv2D, MaxPool2D, Dense, GlobalAveragePooling2D

from datetime import datetime
import tensorflow as tf
import sys

def showImages(pics,labels,num):
    for index, (image, label) in enumerate(zip(pics[0:5], labels[0:5])):
        plt.subplot(1, num, index + 1)
        #print(index, image, label)
        plt.imshow(image, cmap=plt.cm.gray)
        plt.title('Image: %i\n' % label, fontsize=12)
    plt.show()


def getDigits(show=False):
    (x_train, t_train), (x_test, t_test) = mnist.load_data()
    if show:
        showImages(x_train, t_train,5)
    return x_train,t_train,x_test, t_test







# Load train and test data
train_images, train_labels, test_images, test_labels = getDigits()

# Normalize color values (here: grey-scales)
train_images = train_images / 255.0
test_images = test_images / 255.0


print(train_images.shape)

train_images = train_images.reshape(train_images.shape[0], 28, 28, 1)
test_images = test_images.reshape(test_images.shape[0], 28, 28, 1)

print(train_images.shape)


# Do one-hot encoding / do categorical conversion
train_labels = tf.keras.utils.to_categorical(train_labels)
test_labels = tf.keras.utils.to_categorical(test_labels)

num_epochs=5

# Extract number of classes from data dimensions
classes = np.shape(train_labels)[1]

logdir = "logs/scalars/" + datetime.now().strftime("%Y%m%d-%H%M%S")
tensorboard_callback = tf.keras.callbacks.TensorBoard(log_dir=logdir,write_graph=True)


# Define model architecture
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
model.compile(optimizer='sgd', loss='categorical_crossentropy', metrics=['accuracy'])



# Train model
training_history = model.fit(
    train_images, # input
    train_labels, # output
    batch_size=32,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    epochs=num_epochs,
    validation_data=(test_images,test_labels),
    callbacks=[tensorboard_callback],
)

model.summary()
# Evaluate model
#test_loss, test_acc = model.evaluate(test_images, test_labels)
#print('Test accuracy:', test_acc)


# look into the model


tfjs.converters.save_keras_model(model, '../saved_models')

import tensorflow as tf
import tensorflowjs as tfjs
import pandas as pd
import numpy as np
from tensorflow import keras
from keras.callbacks import EarlyStopping
from sklearn.model_selection import train_test_split


test = pd.read_csv('learningData/emnist-letters-test.csv')
train = pd.read_csv('learningData/emnist-letters-train.csv')


#training_letters
train_y = np.array(train.iloc[:,0].values)
train_x = np.array(train.iloc[:,1:].values)
#testing_labels
test_y2 = np.array(test.iloc[:,0].values)
test_x2 = np.array(test.iloc[:,1:].values)

print(max(train_y))
print(train_x)

# Normalise and reshape data
train_images = train_x / 255.0
test_images = test_x2 / 255.0

train_images_number = train_images.shape[0]
train_images_height = 28
train_images_width = 28
train_images_size = train_images_height*train_images_width

train_images = train_images.reshape(train_images_number, train_images_height, train_images_width, 1)

test_images_number = test_images.shape[0]
test_images_height = 28
test_images_width = 28
test_images_size = test_images_height*test_images_width

test_images = test_images.reshape(test_images_number, test_images_height, test_images_width, 1)


from sklearn.model_selection import train_test_split

# split training and validation data using sklearn
x_train,x_val,y_train,y_val = train_test_split(train_images,train_y,test_size=0.2,random_state = 42)

print('trianing set: ', x_train.shape, y_train.shape)
print('validation set: ', x_val.shape, y_val.shape)
print('test set: ', x_val.shape, y_val.shape)

y_train = keras.utils.to_categorical(y_train)
y_val = keras.utils.to_categorical(y_val)

number_of_classes = 27

early_stopping = EarlyStopping(monitor='val_loss', patience=3)

model = tf.keras.Sequential([
    tf.keras.layers.Conv2D(32,3,input_shape=(28,28,1)),
    tf.keras.layers.MaxPooling2D(2,2),
    tf.keras.layers.Flatten(input_shape=(28,28,1)),
    tf.keras.layers.Dense(512,activation='relu'),
    tf.keras.layers.Dense(128,activation='relu'),
    tf.keras.layers.Dense(number_of_classes,activation='softmax')
])

model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])


model.fit(
    x_train,
    y_train,
    batch_size=128,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    validation_data=(x_val, y_val),
    epochs=40,
    callbacks=[early_stopping]
)
test_loss,test_acc = model.evaluate(x_val, y_val)
print('Test accuracy:', test_acc)
model.summary()

tfjs.converters.save_keras_model(model, 'saved_models/Buchstaben')
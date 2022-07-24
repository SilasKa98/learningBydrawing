import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping
import os
import numpy as np
from PIL import Image

# split the mnist data into train and test
(train_img, train_label), (test_img, test_label) = keras.datasets.mnist.load_data()



# reshape and scale the data
train_img = train_img.reshape([train_img.shape[0], 28, 28, 1])
test_img = test_img.reshape([test_img.shape[0], 28, 28, 1])

print(len(train_img))
print(len(test_img))

# To load images to features and labels
def load_images_to_data(image_directory, features_data, label_data):
    list_of_files = os.listdir(image_directory)
    for file in list_of_files:
        image_file_name = os.path.join(image_directory, file)
        if ".jpg" in image_file_name:
            img = Image.open(image_file_name).convert("L")
            img = np.resize(img, (28,28,1))
            im2arr = np.array(img)
            im2arr = im2arr.reshape(1,28,28,1)
            features_data = np.append(features_data, im2arr, axis=0)

            image_label = os.path.basename(file).split("_")[0]

            label_data = np.append(label_data, [image_label], axis=0)
    return features_data, label_data


# Load your own images to training and test data
train_img, train_label = load_images_to_data('templates/savedImages/Zahlen', train_img, train_label)
test_img, test_label = load_images_to_data('templates/savedImages/Zahlen', test_img, test_label)

print(len(train_img))
print(len(test_img))

#normalize data
train_img = train_img / 255.0
test_img = test_img / 255.0

# convert class vectors to binary class matrices --> one-hot encoding
train_label = keras.utils.to_categorical(train_label)
test_label = keras.utils.to_categorical(test_label)

early_stopping = EarlyStopping(monitor='val_loss', patience=6)

model = keras.Sequential([
    keras.layers.Conv2D(32, (5, 5), padding="same", input_shape=[28, 28, 1]),
    keras.layers.MaxPool2D((2,2)),
    keras.layers.Conv2D(64, (5, 5), padding="same"),
    keras.layers.MaxPool2D((2,2)),
    keras.layers.Flatten(),
    keras.layers.Dense(1024, activation='relu'),
    keras.layers.Dropout(0.2),
    keras.layers.Dense(10, activation='softmax'),
])
model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])


model.fit(
    train_img,
    train_label,
    batch_size=128,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    validation_data=(test_img, test_label),
    epochs=40,
    callbacks=[early_stopping]
)
test_loss,test_acc = model.evaluate(test_img, test_label)
print('Test accuracy:', test_acc)
model.summary()

tfjs.converters.save_keras_model(model, 'saved_models/zahlen')
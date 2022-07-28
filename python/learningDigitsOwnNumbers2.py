import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping, ReduceLROnPlateau
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
            img = np.resize(img, (1,28,28,1))
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


#Our input data is in the form of unit8. Convert that into float.
train_img = train_img.astype('float32')
test_img = test_img.astype('float32')

#normalize data
train_img = train_img / 255.0
test_img = test_img / 255.0

# convert class vectors to binary class matrices --> one-hot encoding
train_label = keras.utils.to_categorical(train_label)
test_label = keras.utils.to_categorical(test_label)

early_stopping = EarlyStopping(monitor='val_loss', patience=10)
variable_learning_rate = ReduceLROnPlateau(monitor='val_loss', factor = 0.2, patience = 4)

model = keras.Sequential([
keras.layers.Conv2D(filters = 32, kernel_size = 5, strides = 1, activation = "relu", input_shape=[28, 28, 1]),
keras.layers.Conv2D(filters = 32, kernel_size = 5, strides = 1, use_bias=False),
keras.layers.BatchNormalization(),
# — — — — — — — — — — — — — — — — #
keras.layers.Activation("relu"),
keras.layers.MaxPooling2D(pool_size = 2, strides = 2),
keras.layers.Dropout(0.25),
# — — — — — — — — — — — — — — — — #
keras.layers.Conv2D(filters = 64, kernel_size = 3, strides = 1, activation = "relu"),
keras.layers.Conv2D(filters = 64, kernel_size = 3, strides = 1, use_bias=False),
keras.layers.BatchNormalization(),
# — — — — — — — — — — — — — — — — #
keras.layers.Activation("relu"),
keras.layers.MaxPooling2D(pool_size = 2, strides = 2),
keras.layers.Dropout(0.25),
keras.layers.Flatten(),
# — — — — — — — — — — — — — — — — #
keras.layers.Dense(units = 256, use_bias=False),
keras.layers.BatchNormalization(),
# — — — — — — — — — — — — — — — — #
keras.layers.Activation("relu"),
# — — — — — — — — — — — — — — — — #
keras.layers.Dense(units = 128, use_bias=False),
keras.layers.BatchNormalization(),
# — — — — — — — — — — — — — — — — #
keras.layers.Activation("relu"),
# — — — — — — — — — — — — — — — — #
keras.layers.Dense(units = 84, use_bias=False),
keras.layers.BatchNormalization(),
# — — — — — — — — — — — — — — — — #
keras.layers.Activation("relu"),
keras.layers.Dropout(0.25),
# — — — — — — — — — — — — — — — — #
# Output
keras.layers.Dense(units = 10, activation = "softmax")
])


model.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])


model.fit(
    train_img,
    train_label,
    batch_size=128,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    validation_data=(test_img, test_label),
    epochs=40,
    callbacks=[variable_learning_rate,early_stopping]
)
test_loss,test_acc = model.evaluate(test_img, test_label)
print('Test accuracy:', test_acc)
model.summary()

tfjs.converters.save_keras_model(model, 'saved_models/zahlen')
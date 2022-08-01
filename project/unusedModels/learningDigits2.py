import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping

# split the mnist data into train and test
(train_img, train_label), (test_img, test_label) = keras.datasets.mnist.load_data()

#normalize data
train_img = train_img / 255.0
test_img = test_img / 255.0

# reshape and scale the data
train_img = train_img.reshape([train_img.shape[0], 28, 28, 1])
test_img = test_img.reshape([test_img.shape[0], 28, 28, 1])

# convert class vectors to binary class matrices --> one-hot encoding
train_label = keras.utils.to_categorical(train_label)
test_label = keras.utils.to_categorical(test_label)

early_stopping = EarlyStopping(monitor='val_loss', patience=3)

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

tfjs.converters.save_keras_model(model, 'saved_models')
import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping

#########################
#this model is working !#
#########################


from emnist import extract_training_samples, extract_test_samples
train_img, train_label = extract_training_samples('letters')
test_img, test_label = extract_test_samples('letters')

print(train_label)
print(max(train_label))
print(test_label)
print(max(test_label))

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

number_of_classes = 27
'''
model = tf.keras.Sequential([
    tf.keras.layers.Conv2D(32,3,input_shape=(28,28,1)),
    tf.keras.layers.MaxPooling2D(2,2),
    tf.keras.layers.Flatten(input_shape=(28,28,1)),
    tf.keras.layers.Dense(512,activation='relu'),
    tf.keras.layers.Dense(128,activation='relu'),
    tf.keras.layers.Dense(number_of_classes,activation='softmax')
])
'''

model = tf.keras.Sequential()

model.add(tf.keras.layers.Conv2D(filters=128, kernel_size=(5,5), padding = 'same', activation='relu',input_shape=(28,28,1)))
model.add(tf.keras.layers.MaxPooling2D(pool_size=(2,2), strides=(2,2)))
model.add(tf.keras.layers.Conv2D(filters=64, kernel_size=(3,3) , padding = 'same', activation='relu'))
model.add(tf.keras.layers.MaxPooling2D(pool_size=(2,2)))
model.add(tf.keras.layers.Flatten())
model.add(tf.keras.layers.Dense(units=128, activation='relu'))
model.add(tf.keras.layers.Dropout(.5))
model.add(tf.keras.layers.Dense(units=number_of_classes, activation='softmax'))

model.summary()

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

tfjs.converters.save_keras_model(model, 'saved_models/buchstaben')
import tensorflow as tf
import tensorflowjs as tfjs
from tensorflow import keras
from keras.callbacks import EarlyStopping
import os
import numpy as np # linear algebra
from sklearn.model_selection import train_test_split

# variables
batch_size = 64
SEED = 444

# se carga la data de entrenamiento y prueba

os.listdir('learningData/kuzushiji')

train_set = np.load('learningData/kuzushiji/kmnist-train-imgs.npz')['arr_0']
train_labels = np.load('learningData/kuzushiji/kmnist-train-labels.npz')['arr_0']

test_set = np.load('learningData/kuzushiji/kmnist-test-imgs.npz')['arr_0']
test_labels = np.load('learningData/kuzushiji/kmnist-test-labels.npz')['arr_0']
num_class = len(np.unique(train_labels))

print(train_labels)

train_labels = keras.utils.to_categorical(train_labels, num_class)
test_labels = keras.utils.to_categorical(test_labels, num_class)

# se crea el set de validación
train_set, validation_set, train_labels, validation_labels = train_test_split(train_set, train_labels, test_size = 0.1, random_state = SEED)

# normalización del dataset
train_set = train_set.reshape(train_set.shape[0], train_set.shape[1], train_set.shape[2], 1)/255
validation_set = validation_set.reshape(validation_set.shape[0], validation_set.shape[1], validation_set.shape[2], 1)/255
test_set = test_set.reshape(test_set.shape[0], test_set.shape[1], test_set.shape[2], 1)/255

# dimensiones del dataset
print(f"""
Train set dimensions: {train_set.shape}
Validation set dimensions: {validation_set.shape}
Test set dimensions: {test_set.shape}
""")


early_stopping = EarlyStopping(monitor='val_loss', patience=5)

model = tf.keras.Sequential()
# primera capa
model.add(tf.keras.layers.Conv2D(32,(5,5),activation = 'relu', input_shape = (28,28,1), padding="same"))
model.add(tf.keras.layers.MaxPooling2D(2,2))
model.add(tf.keras.layers.BatchNormalization())
# segunda capa
model.add(tf.keras.layers.Conv2D(64,(5,5),activation = 'relu'))
# tercera capa
model.add(tf.keras.layers.Conv2D(64,(5,5),activation = 'relu'))
model.add(tf.keras.layers.Flatten())
# cuarta capa
model.add(tf.keras.layers.Dense(64,activation = 'relu'))
model.add(tf.keras.layers.Dropout(0.4))
# quinta capa
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
)


tfjs.converters.save_keras_model(model, 'saved_models/hiragana')
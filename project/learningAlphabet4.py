import tensorflow as tf
import tensorflowjs as tfjs
from keras.optimizers import RMSprop

from keras_preprocessing.image import ImageDataGenerator
from tensorflow import keras
from keras.callbacks import EarlyStopping, ReduceLROnPlateau


from emnist import extract_training_samples, extract_test_samples

#get training and test data
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

#drop out of training if its not getting better for the defined amount of epochs (patience)
early_stopping = EarlyStopping(monitor='val_loss', patience=3)

number_of_classes = 27

#define a sequential model and add the layers to it
model = keras.Sequential()

model.add(keras.layers.Conv2D(filters = 32, kernel_size = (5,5),padding = 'Same', activation ='relu', input_shape = (28,28,1)))
model.add(keras.layers.Conv2D(filters = 32, kernel_size = (5,5),padding = 'Same', activation ='relu'))
model.add(keras.layers.MaxPool2D(pool_size=(2,2)))
model.add(keras.layers.Dropout(0.25))


model.add(keras.layers.Conv2D(filters = 64, kernel_size = (3,3),padding = 'Same', activation ='relu'))
model.add(keras.layers.Conv2D(filters = 64, kernel_size = (3,3),padding = 'Same', activation ='relu'))
model.add(keras.layers.MaxPool2D(pool_size=(2,2), strides=(2,2)))
model.add(keras.layers.Dropout(0.25))

model.add(keras.layers.Flatten())
model.add(keras.layers.Dense(256, activation = "relu"))
model.add(keras.layers.Dropout(0.5))
model.add(keras.layers.Dense(27, activation = "softmax"))


# Define the optimizer
optimizer = RMSprop(learning_rate=0.001, rho=0.9, epsilon=1e-08, decay=0.0)

model.compile(optimizer=optimizer, loss='categorical_crossentropy', metrics=['accuracy'])

#variable learning rate for better results
learning_rate_reduction = ReduceLROnPlateau(monitor='val_accuracy',
                                            patience=3,
                                            verbose=1,
                                            factor=0.5,
                                            min_lr=0.00001)



#changing some parameters of the image to get a higher varity of datas
datagen = ImageDataGenerator(
        featurewise_center=False,  # set input mean to 0 over the dataset
        samplewise_center=False,  # set each sample mean to 0
        featurewise_std_normalization=False,  # divide inputs by std of the dataset
        samplewise_std_normalization=False,  # divide each input by its std
        zca_whitening=False,  # apply ZCA whitening
        rotation_range=10,  # randomly rotate images in the range (degrees, 0 to 180)
        zoom_range = 0.1, # Randomly zoom image
        width_shift_range=0.1,  # randomly shift images horizontally (fraction of total width)
        height_shift_range=0.1,  # randomly shift images vertically (fraction of total height)
        horizontal_flip=False,  # randomly flip images
        vertical_flip=False # randomly flip images
)


datagen.fit(train_img)
batch_size = 86
epochs = 30

model.fit(
    train_img,
    train_label,
    verbose=1, # Suppress chatty output; use Tensorboard instead
    steps_per_epoch=train_img.shape[0] // batch_size,
    validation_data=(test_img, test_label),
    epochs=epochs,
    callbacks=[early_stopping,learning_rate_reduction]
)


test_loss,test_acc = model.evaluate(test_img, test_label)
print('Test accuracy:', test_acc)
model.summary()

tfjs.converters.save_keras_model(model, 'saved_models/buchstaben')
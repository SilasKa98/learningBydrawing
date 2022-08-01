import numpy as np # linear algebra
import pandas as pd # data processing, CSV file I/O (e.g. pd.read_csv)

import tensorflow as tf
from keras.layers import Dense, Activation, Dropout, Flatten, BatchNormalization, Input, concatenate, Reshape
from keras import layers, Input, Model

from keras.models import Sequential, save_model

from keras.callbacks import EarlyStopping

import tensorflowjs as tfjs

import pandas as pd
import random


print('Done..')


train=pd.read_csv('learningData/emnist-digits-train.csv')
test=pd.read_csv('learningData/emnist-digits-test.csv')
a=['label']
c=0
for i in range(784):
    a.append('pixel'+str(i))
train.columns=a
test.columns=a

df=pd.read_csv('learningData/train.csv')
X=df.drop('label', axis=1)
y=df.label
df.head()

x_train=df.drop('label', axis=1)
Y_train=df.label
x_train=x_train.values
Y_train=Y_train.values

import matplotlib.pyplot as plt
print('Some examples from training set...')
# look at some of the digits from train_X
plt.figure(figsize=(15,6))
for i in range(40):
    plt.subplot(4, 10, i+1)
    plt.imshow(x_train[i].reshape((28,28)),cmap=plt.cm.binary)
    plt.title("label=%d" % Y_train[i],y=0.9)
    plt.axis('off')
plt.subplots_adjust(wspace=0.3, hspace=-0.1)
plt.show()

x_train=train.drop('label', axis=1)
Y_train=train.label
x_train=x_train.values
Y_train=Y_train.values

import matplotlib.pyplot as plt
print('Some examples from additional dataset...')
plt.figure(figsize=(15,6))
for i in range(40):
    plt.subplot(4, 10, i+1)
    plt.imshow(x_train[i].reshape((28,28)),cmap=plt.cm.binary)
    plt.title("label=%d" % Y_train[i],y=0.9)
    plt.axis('off')
plt.subplots_adjust(wspace=0.3, hspace=-0.1)
plt.show()

del x_train
del Y_train

df=pd.concat([df,train,test])
df.reset_index()

train=pd.read_csv('learningData/emnist-mnist-train.csv')
test=pd.read_csv('learningData/emnist-mnist-test.csv')
train.columns=a
test.columns=a


df=pd.concat([df,train,test])
df.reset_index()

df = df.sample(frac = 1)
X=df.drop('label', axis=1)
y=df.label
y=pd.get_dummies(y)

from sklearn.model_selection import train_test_split

X_train, X_validation, y_train, y_validation = train_test_split(X, y, test_size=0.15, random_state=42)
test_df=pd.read_csv('learningData/test.csv')
X_test = test_df

del X
del y
del df


def create_model(num_column):
    model = Sequential()
    model.add(tf.keras.layers.Reshape((28, 28, 1), input_shape=(784,)))
    model.add(BatchNormalization())
    model.add(layers.Conv2D(64, (3, 3), activation='relu', input_shape=(28, 28, 1)))
    model.add(layers.MaxPooling2D((2, 2)))
    model.add(BatchNormalization())
    model.add(Dropout(0.5))
    model.add(layers.Conv2D(128, (3, 3), activation='relu'))
    model.add(layers.MaxPooling2D((2, 2)))
    model.add(BatchNormalization())
    model.add(Dropout(0.5))
    model.add(layers.Conv2D(256, (3, 3), activation='relu'))
    model.add(BatchNormalization())
    model.add(Dropout(0.5))

    model.add(Flatten())
    model.add(Dense(units=50, activation='relu'))
    model.add(Dense(units=100, activation='relu'))
    model.add(Dropout(0.3))
    model.add(Dense(units=10, activation='softmax'))
    model.compile(
        optimizer='adam', loss='categorical_crossentropy', metrics=tf.keras.metrics.CategoricalAccuracy())
    return model


# detect and init the TPU
tpu = tf.distribute.cluster_resolver.TPUClusterResolver.connect()

# instantiate a distribution strategy
tpu_strategy = tf.distribute.experimental.TPUStrategy(tpu)

# instantiating the model in the strategy scope creates the model on the TPU
with tpu_strategy.scope():
    model=create_model(784)
    #model.compile(optimizer=tf.keras.optimizers.Adam(), loss='mse', metrics=['mae'])
model.summary()

es = EarlyStopping(monitor='val_categorical_accuracy', mode='max', verbose=1, patience=175)



history=model.fit(X_train, y_train, epochs=1000, validation_data=(X_validation, y_validation), batch_size=20480, verbose=1,callbacks=[es])

tfjs.converters.save_keras_model(model, 'saved_models')
from flask import Flask, render_template
from flask import request
from flask_restful import Resource, Api
import pickle
import pandas as pd
from flask_cors import CORS

#model = pickle.load(open("saved_models/letters.pkl", "rb"))

app = Flask(__name__)


@app.route('/', methods=['POST', 'GET'])
def loadSite():
    return render_template('index.html')


@app.route('/predict', methods=['POST'])
def processData():
    drawing = request.form['drawing']
    print(drawing)

if __name__ == '__main__':
    app.run(debug=True)

'''
CORS(app)

#api object creation
api = Api(app)

#prediction api call

class prediction(Resource):
    def get(self, drawnLetter):
        #maybe do some stuff with the pic...
        model = pickle.load(open("saved_models/letters.pkl"), "rb")
        prediction = model.prediction(drawnLetter)
        return str(prediction)


api.add_resource(prediction, "/prediction/<int:drawnLetter>")
'''



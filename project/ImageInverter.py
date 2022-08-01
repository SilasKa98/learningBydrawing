from PIL import Image, ImageChops
import glob

filename_list = []
inv_filename_list = []
path_base = "learningData\\six-shapes-dataset-v1\\six-shapes\\*\\*\\*.png"
path_inverted = "learningData\\six-shapes-dataset-v1-inverted\\six-shapes"

for filename in glob.glob(path_base):
    filename_list.append(filename)

for path in filename_list:
    split = path.split('\\')
    usage = split.__getitem__(split.__len__()-3)
    geometric_shape = split.__getitem__(split.__len__()-2)
    filename = split.__getitem__(split.__len__()-1)

    img = Image.open(path)
    inv_img = ImageChops.invert(img)
    inv_img.save(path_inverted+"\\"+usage+"\\"+geometric_shape+"\\"+filename)

for inv_filename in glob.glob(path_inverted+"\\*\\*\\*.png"):
    inv_filename_list.append(inv_filename)

print("Number of Base Images: ", filename_list.__len__())
print("Number of Inverted Images: ", inv_filename_list.__len__())
print("Inversion done!")

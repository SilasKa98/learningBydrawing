from PIL import Image, ImageChops
import glob

filename_list = []
inv_filename_list = []

# Folder Path with all Images to invert
path_base = "learningData\\six-shapes-dataset-v1\\six-shapes\\*\\*\\*.png"
# Folder Path to which the new Images should be saved
path_inverted = "learningData\\six-shapes-dataset-v1-inverted\\six-shapes"

# get all image paths for the folder defined in path_base
for filename in glob.glob(path_base):
    filename_list.append(filename)

# opens all images in the list and inverts them
for path in filename_list:
    split = path.split('\\')
    # variables to create the same structure as the original image
    usage = split.__getitem__(split.__len__()-3)
    geometric_shape = split.__getitem__(split.__len__()-2)
    filename = split.__getitem__(split.__len__()-1)

    img = Image.open(path)
    inv_img = ImageChops.invert(img)    # invert the image (black to white and white to black for example)
    inv_img.save(path_inverted+"\\"+usage+"\\"+geometric_shape+"\\"+filename)   # saves inverted image to path_inverted

# get all image paths for the folder defined in path_inverted to check numbers
for inv_filename in glob.glob(path_inverted+"\\*\\*\\*.png"):
    inv_filename_list.append(inv_filename)

# some Prints for Information
print("Number of Base Images: ", filename_list.__len__())
print("Number of Inverted Images: ", inv_filename_list.__len__())
print("Inversion done!")

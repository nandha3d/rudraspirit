import os
import shutil

shiva_dir = r'd:\PROJECTS\WEBSITES\rudraspirit\shivarudraksha'
public_dir = r'd:\PROJECTS\WEBSITES\rudraspirit\public'

# 1. Copy index.html
src_index = os.path.join(shiva_dir, 'dist', 'index.html')
dst_index = os.path.join(public_dir, 'index.html')
shutil.copyfile(src_index, dst_index)
print("Updated public/index.html")

# 2. Copy assets
src_assets = os.path.join(shiva_dir, 'dist', 'assets')
dst_assets = os.path.join(public_dir, 'assets')
os.makedirs(dst_assets, exist_ok=True)
for item in os.listdir(src_assets):
    s = os.path.join(src_assets, item)
    d = os.path.join(dst_assets, item)
    if os.path.isfile(s):
        shutil.copyfile(s, d)
        print(f"Copied asset: {item}")

# 3. Copy public/images
src_images = os.path.join(shiva_dir, 'public', 'images')
dst_images = os.path.join(public_dir, 'images')
os.makedirs(dst_images, exist_ok=True)
for root, dirs, files in os.walk(src_images):
    rel_path = os.path.relpath(root, src_images)
    target_root = os.path.join(dst_images, rel_path)
    os.makedirs(target_root, exist_ok=True)
    for f in files:
        s_file = os.path.join(root, f)
        d_file = os.path.join(target_root, f)
        shutil.copyfile(s_file, d_file)
print("Synced all images to public/images")

# 4. Copy public/data
src_data = os.path.join(shiva_dir, 'public', 'data')
dst_data = os.path.join(public_dir, 'data')
os.makedirs(dst_data, exist_ok=True)
for f in os.listdir(src_data):
    shutil.copyfile(os.path.join(src_data, f), os.path.join(dst_data, f))
print("Synced data to public/data")

print("All synchronized successfully!")

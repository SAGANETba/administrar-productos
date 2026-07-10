from PIL import Image, ImageDraw, ImageFont

def make_image(path, text, color):
    img = Image.new("RGB", (400, 400), color)
    draw = ImageDraw.Draw(img)
    try:
        font = ImageFont.truetype("arial.ttf", 34)
    except Exception:
        font = ImageFont.load_default()
    bbox = draw.multiline_textbbox((0, 0), text, font=font, align="center")
    w, h = bbox[2] - bbox[0], bbox[3] - bbox[1]
    draw.multiline_text(((400 - w) / 2, (400 - h) / 2), text, fill="white", font=font, align="center")
    img.save(path, "PNG")
    print("OK", path)

base = r"C:\xampp\htdocs\administrar-productos\storage\app\test-assets"

make_image(f"{base}\\producto-valido.png", "Producto\nValido", (44, 110, 73))
make_image(f"{base}\\producto-actualizado.png", "Producto\nActualizado", (26, 77, 46))

# Imagenes de reemplazo para los productos que ya existen en la base de datos
make_image(f"{base}\\mouse.png", "Mouse\nInalambrico", (46, 91, 143))
make_image(f"{base}\\audifonos.png", "Audifonos\nBluetooth", (156, 87, 27))

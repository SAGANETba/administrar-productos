Add-Type -AssemblyName System.Net.Http

$base = "http://127.0.0.1:8000/api/products"
$assets = "C:\xampp\htdocs\administrar-productos\storage\app\test-assets"
$out = "$assets\evidencias.txt"
Remove-Item $out -ErrorAction SilentlyContinue

function Log($title, $req, $status, $body) {
    Add-Content -Path $out -Value "`n===== $title ====="
    Add-Content -Path $out -Value "REQUEST: $req"
    Add-Content -Path $out -Value "RESPONSE: HTTP $status"
    Add-Content -Path $out -Value "$body"
}

$client = New-Object System.Net.Http.HttpClient
$client.DefaultRequestHeaders.Accept.Add((New-Object System.Net.Http.Headers.MediaTypeWithQualityHeaderValue("application/json")))

function Send-Multipart($method, $url, $fields, $fileField, $filePath, $fileName, $contentType) {
    $content = New-Object System.Net.Http.MultipartFormDataContent
    foreach ($k in $fields.Keys) {
        $content.Add((New-Object System.Net.Http.StringContent($fields[$k])), $k)
    }
    if ($filePath) {
        $bytes = [System.IO.File]::ReadAllBytes($filePath)
        $fileContent = New-Object System.Net.Http.ByteArrayContent(,$bytes)
        $fileContent.Headers.ContentType = [System.Net.Http.Headers.MediaTypeHeaderValue]::Parse($contentType)
        $content.Add($fileContent, $fileField, $fileName)
    }
    $request = New-Object System.Net.Http.HttpRequestMessage($method, $url)
    $request.Content = $content
    $response = $client.SendAsync($request).GetAwaiter().GetResult()
    $body = $response.Content.ReadAsStringAsync().GetAwaiter().GetResult()
    return @{ status = [int]$response.StatusCode; body = $body }
}

function Send-Simple($method, $url) {
    $request = New-Object System.Net.Http.HttpRequestMessage($method, $url)
    $response = $client.SendAsync($request).GetAwaiter().GetResult()
    $body = $response.Content.ReadAsStringAsync().GetAwaiter().GetResult()
    return @{ status = [int]$response.StatusCode; body = $body }
}

# CP-01: Crear producto con imagen correctamente
$fields = @{
    name = "Teclado Mecanico RGB"
    description = "Teclado mecanico retroiluminado, switches azules"
    price = "899.99"
    stock = "25"
}
$r1 = Send-Multipart ([System.Net.Http.HttpMethod]::Post) $base $fields "image" "$assets\producto-valido.png" "producto-valido.png" "image/png"
Log "CP-01 Crear producto con imagen (POST /api/products)" "multipart/form-data name,description,price,stock,image=producto-valido.png" $r1.status $r1.body
$product = $r1.body | ConvertFrom-Json
$productId = $product.data.id
"PRODUCT_ID=$productId" | Out-File "$assets\product_id.txt"

# CP-06: Crear producto sin campos obligatorios
$r6 = Send-Multipart ([System.Net.Http.HttpMethod]::Post) $base @{ name = "" } $null $null $null $null
Log "CP-06 Crear producto sin campos obligatorios (POST /api/products)" "multipart/form-data con solo name vacio, sin description/price/stock/image" $r6.status $r6.body

# CP-07: Subir archivo que no es imagen
$fieldsInvalid = @{
    name = "Producto Invalido"
    description = "Prueba de archivo no valido"
    price = "10"
    stock = "1"
}
$r7 = Send-Multipart ([System.Net.Http.HttpMethod]::Post) $base $fieldsInvalid "image" "$assets\archivo-invalido.txt" "archivo-invalido.txt" "text/plain"
Log "CP-07 Subir archivo que no es imagen (POST /api/products)" "multipart/form-data con image=archivo-invalido.txt (text/plain)" $r7.status $r7.body

# CP-02: Listar productos
$r2 = Send-Simple ([System.Net.Http.HttpMethod]::Get) $base
Log "CP-02 Listar productos (GET /api/products)" "sin body" $r2.status $r2.body

# CP-03: Consultar detalle de producto con imagen
$r3 = Send-Simple ([System.Net.Http.HttpMethod]::Get) "$base/$productId"
Log "CP-03 Consultar detalle de producto (GET /api/products/$productId)" "sin body" $r3.status $r3.body

# CP-04: Actualizar datos del producto (incluye nueva imagen). Laravel requiere _method=PUT en POST multipart.
$fieldsUpdate = @{
    name = "Teclado Mecanico RGB PRO"
    description = "Teclado mecanico retroiluminado, switches rojos, edicion PRO"
    price = "999.99"
    stock = "40"
    _method = "PUT"
}
$r4 = Send-Multipart ([System.Net.Http.HttpMethod]::Post) "$base/$productId" $fieldsUpdate "image" "$assets\producto-actualizado.png" "producto-actualizado.png" "image/png"
Log "CP-04 Actualizar producto y su imagen (PUT /api/products/$productId, enviado como POST + _method=PUT)" "multipart/form-data name,description,price,stock,image=producto-actualizado.png,_method=PUT" $r4.status $r4.body

# Verificacion adicional: la imagen asociada es accesible publicamente vía /storage
$productAfterUpdate = $r4.body | ConvertFrom-Json
$imageUrl = $productAfterUpdate.data.image.url
$imgReq = New-Object System.Net.Http.HttpRequestMessage([System.Net.Http.HttpMethod]::Get, $imageUrl)
$imgResp = $client.SendAsync($imgReq).GetAwaiter().GetResult()
$imgBytes = $imgResp.Content.ReadAsByteArrayAsync().GetAwaiter().GetResult()
Log "Verificacion: acceso publico al archivo de imagen (GET $imageUrl)" "sin body" ([int]$imgResp.StatusCode) "Content-Type: $($imgResp.Content.Headers.ContentType)  Bytes: $($imgBytes.Length)"

# CP-08: Consultar producto inexistente
$r8 = Send-Simple ([System.Net.Http.HttpMethod]::Get) "$base/999999"
Log "CP-08 Consultar producto inexistente (GET /api/products/999999)" "sin body" $r8.status $r8.body

# CP-05: Eliminar producto
$r5 = Send-Simple ([System.Net.Http.HttpMethod]::Delete) "$base/$productId"
Log "CP-05 Eliminar producto (DELETE /api/products/$productId)" "sin body" $r5.status $r5.body

# Verificacion adicional: consultar producto ya eliminado
$r9 = Send-Simple ([System.Net.Http.HttpMethod]::Get) "$base/$productId"
Log "Verificacion post-DELETE: consultar producto eliminado (GET /api/products/$productId)" "sin body" $r9.status $r9.body

# Producto de demostracion que se deja registrado en la base de datos entregada
$fieldsDemo = @{
    name = "Mouse Inalambrico Ergonomico"
    description = "Mouse inalambrico con sensor optico de alta precision"
    price = "349.50"
    stock = "60"
}
$rDemo = Send-Multipart ([System.Net.Http.HttpMethod]::Post) $base $fieldsDemo "image" "$assets\producto-valido.png" "producto-valido.png" "image/png"
Log "Producto de demostracion dejado en la base de datos entregada (POST /api/products)" "multipart/form-data name,description,price,stock,image=producto-valido.png" $rDemo.status $rDemo.body

Get-Content $out

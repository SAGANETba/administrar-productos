Add-Type -AssemblyName System.Net.Http

$cookies = New-Object System.Net.CookieContainer
$handler = New-Object System.Net.Http.HttpClientHandler
$handler.CookieContainer = $cookies
$handler.AllowAutoRedirect = $true
$client = New-Object System.Net.Http.HttpClient($handler)
$base = "http://127.0.0.1:8000"

function Get-CsrfToken($html) {
    if ($html -match 'name="csrf-token" content="([^"]+)"') { return $matches[1] }
    if ($html -match 'name="_token" type="hidden" value="([^"]+)"') { return $matches[1] }
    if ($html -match '_token[^>]*value="([^"]+)"') { return $matches[1] }
    return $null
}

function Get-Html($url) {
    $resp = $client.GetAsync($url).GetAwaiter().GetResult()
    return $resp.Content.ReadAsStringAsync().GetAwaiter().GetResult()
}

function Post-Form($url, $fields) {
    $pairs = New-Object 'System.Collections.Generic.List[System.Collections.Generic.KeyValuePair[string,string]]'
    foreach ($k in $fields.Keys) {
        $pairs.Add((New-Object 'System.Collections.Generic.KeyValuePair[string,string]'($k, [string]$fields[$k])))
    }
    $content = New-Object System.Net.Http.FormUrlEncodedContent -ArgumentList (,$pairs)
    $resp = $client.PostAsync($url, $content).GetAwaiter().GetResult()
    return $resp
}

Write-Host "=== 1. Registro ==="
$html = Get-Html "$base/register"
$token = Get-CsrfToken $html
$email = "cliente" + (Get-Random -Maximum 99999) + "@example.com"
$resp = Post-Form "$base/register" @{
    _token = $token
    name = "Cliente de Prueba"
    email = $email
    password = "password123"
    password_confirmation = "password123"
}
"Status final tras registro: $([int]$resp.StatusCode)  URL: $($resp.RequestMessage.RequestUri)"

Write-Host "`n=== 2. Verificar que quedo logueado (dashboard) ==="
$dashHtml = Get-Html "$base/dashboard"
("Contiene 'Dashboard': " + ($dashHtml -match "Dashboard"))

Write-Host "`n=== 3. Agregar producto 1 al carrito ==="
$html = Get-Html "$base/"
$token = Get-CsrfToken $html
$resp = Post-Form "$base/carrito/1" @{ _token = $token; quantity = "2" }
"Status: $([int]$resp.StatusCode)  URL final: $($resp.RequestMessage.RequestUri)"

Write-Host "`n=== 4. Ver carrito ==="
$cartHtml = Get-Html "$base/carrito"
("Contiene 'Mouse Inalambrico': " + ($cartHtml -match "Mouse Inalambrico"))
if ($cartHtml -match 'Total: \$([0-9,.]+)') { "Total mostrado: $($matches[1])" }

Write-Host "`n=== 5. Ir a checkout ==="
$checkoutHtml = Get-Html "$base/checkout"
$token = Get-CsrfToken $checkoutHtml
("Checkout carga bien: " + ($checkoutHtml -match "Resumen del pedido"))

Write-Host "`n=== 6. Confirmar pago (simulado) ==="
$resp = Post-Form "$base/checkout" @{
    _token = $token
    nombre_cliente = "Cliente de Prueba"
    direccion_envio = "Calle Falsa 123, Pachuca"
}
"Status: $([int]$resp.StatusCode)  URL final: $($resp.RequestMessage.RequestUri)"
$finalHtml = $resp.Content.ReadAsStringAsync().GetAwaiter().GetResult()
("Contiene numero de pedido: " + ($finalHtml -match "Pedido #"))
("Contiene mensaje de exito: " + ($finalHtml -match "Pago simulado exitoso"))

Write-Host "`n=== 7. Verificar historial de pedidos ==="
$ordersHtml = Get-Html "$base/mis-pedidos"
("Aparece en mis pedidos: " + ($ordersHtml -match "#1"))

Write-Host "`n=== 8. Verificar que el stock se descontó (via API) ==="
$apiClient = New-Object System.Net.Http.HttpClient
$apiClient.DefaultRequestHeaders.Accept.Add((New-Object System.Net.Http.Headers.MediaTypeWithQualityHeaderValue("application/json")))
$prod = $apiClient.GetAsync("$base/api/products/1").GetAwaiter().GetResult().Content.ReadAsStringAsync().GetAwaiter().GetResult()
$prod

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Envio</title>
    <link href="{{ asset('css/pdf-shipping.css') }}" type="text/css" rel="stylesheet">
</head>

<body>
<div class="shipping-label">
    <div class="label-header">
        <div class="label-info">
            <h1>{{ $data['sender_name'] }}</h1>
            <p>Tracking: {{ $data['tracking'] }}</p>
        </div>
    </div>
    <div class="label-details">
        <div class="shipper-info">
            <h2>Expedidor:</h2>
            <p>{{ $data['sender_name'] }}</p>
            <p>{{ $data['car'] }}</p>
            <p>{{ $data['sender_address_street'] }}, {{ $data['sender_address_postal_code'] }}</p>
            <p>{{ $data['sender_address_place'] }}, {{ $data['sender_address_city'] }}</p>
        </div>
        <div class="receiver-info">
            <h2>Destinatário:</h2>
            <p>{{ $data['receiver_name'] }}</p>
            <p>{{ $data['receiver_address_street'] }}, {{ $data['receiver_address_postal_code'] }}</p>
            <p>{{ $data['receiver_address_city'] }}</p>
        </div>
    </div>
    <div class="label-footer">
        <div class="product-details">
            <h2>Instruções de Entrega:</h2>
            <p>{{ $data['receiver_instructions'] }}</p>
        </div>
        <div class="customs-info">
            <h2>Volume:</h2>
            <p>{{ $volume['weight'] }}</p>
            <p>{{ $volume['orden'] }}/{{ $data['number_of_volumes'] }}</p>
        </div>
    </div>
    <canvas id="barcodeCanvas"></canvas>
</div>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var canvas = document.getElementById('barcodeCanvas');
        JsBarcode(canvas, "123456789012", {
            format: "CODE128",
            lineColor: "#000000",
            width: 1,
            height: 100,
            displayValue: true
        });
    });
</script>
</body>
<style>
    /* General Styles */
    body {
        font-family: sans-serif;
        margin: 0;
        padding: 0;
    }

    /* Ajuste a largura da etiqueta de envio para caber na página A4 */
    .shipping-label {
        margin: 0 auto;
        border: 1px solid #ccc;
        padding: 10px;
        width: 10cm;
        height: 10cm;
    }

    .label-header {
        display: table;
        /* Substitua flexbox por display: table */
        width: 100%;
        /* Garanta que ocupe toda a largura */
        margin-bottom: 20px;
    }

    .label-info,
    .label-instructions {
        text-align: center;
        float: left;
        /* Use float para alinhamento */
    }

    .label-instructions {
        text-align: right;
        float: right;
        /* Use float para alinhamento */
    }

    .label-details {
        clear: both;
        /* Limpa o float para começar uma nova linha */
        display: table;
        /* Substitua flexbox por display: table */
        width: 100%;
        /* Garanta que ocupe toda a largura */
    }

    .shipper-info,
    .receiver-info,
    .product-details,
    .payer-details,
    .customs-info {
        text-align: left;
        display: table-cell;
        /* Use display: table-cell para alinhamento */
        vertical-align: top;
        /* Alinha verticalmente ao topo */
    }

    .label-footer {
        display: table;
        /* Substitua flexbox por display: table */
        width: 100%;
        /* Garanta que ocupe toda a largura */
        margin-top: 20px;
        /* Adiciona margem superior para separação */
    }

    h2 {
        font-size: 12px;
        /* Reduzir o tamanho da fonte para caber melhor */
        margin-bottom: 5px;
    }

    p {
        font-size: 10px;
        margin-bottom: 10px;
    }
</style>
</html>

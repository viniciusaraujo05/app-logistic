<h1>Encomenda #{{ $order['order_code'] }}</h1>
<h2>Detalhes da Encomenda:</h2>
<table>
    <tr>
        <th>Telefone</th>
        <th>Morada</th>
        <th>Cidade</th>
        s
        <th>Estado/Província</th>
        <th>País</th>
    </tr>
    <tr>
        <td>{{ $order['phone'] }}</td>
        <td>{{ $order['shipping_address']['street'] }}</td>
        <td>{{ $order['shipping_address']['city'] }}</td>
        <td>{{ $order['shipping_address']['state'] }}</td>
        <td>{{ $order['shipping_address']['country'] }}</td>
    </tr>
    <tr>
        <th>Destinatário</th>
        <th>Nome Contacto</th>
        <th>Data de Preparação</th>
        <th>Status</th>
    </tr>
    <tr>
        <td>{{ $order['customer_name'] }}</td>
        <td>{{ $order['email'] }}</td>
        <td>{{ date('d-m-Y H:i', strtotime($order['created_at'])) }}</td>
        <td>{{ $order['status']['name'] }}</td>
    </tr>
</table>
@if(isset($order['responsible']))
    <h2>Responsável:</h2>
    <table>
        <tr>
            <td>
                {{ $order['responsible'] }}
            </td>
        </tr>
    </table>
@endif
<h2>Produtos:</h2>
<table>
    <tr>
        <td>
            @foreach ($order['products'] as $produto)
                {{ $produto['name'] }} (qtd: {{ $produto['quantity'] }})<br>
            @endforeach
        </td>
    </tr>
</table>
<style>
    body {
        font-family: Arial, sans-serif;
        /* Fonte padrão */
        margin: 0;
        /* Remove margem do body */
        padding: 20px;
        /* Adiciona padding ao conteúdo */
    }

    h1 {
        text-align: center;
        font-size: 20px;
        /* Aumenta o tamanho do título */
        margin-bottom: 15px;
        /* Adiciona margem inferior */
    }

    h2 {
        font-size: 18px;
        /* Aumenta o tamanho dos subtítulos */
        margin-bottom: 10px;
        /* Adiciona margem inferior */
    }

    table {
        border-collapse: collapse;
        width: 100%;
        border: 1px solid #ddd;
        /* Adiciona borda à tabela */
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
    }

    th {
        text-align: left;
        background-color: #f2f2f2;
        font-weight: bold;
        width: 20px;
        /* Largura fixa para cabeçalhos */
    }

    td {
        font-size: 14px;
        width: 50px;
        /* Largura dupla para dados */
    }
</style>

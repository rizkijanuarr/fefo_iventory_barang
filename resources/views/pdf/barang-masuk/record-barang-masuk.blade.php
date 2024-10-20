<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Print Barang Masuk</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 40px;
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }

        .super-title {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            margin-bottom: -8px;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        header {
            text-align: center;
            margin-bottom: 10px;
        }

        header img {
            width: 60px;
            height: auto;
        }

        header h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 5px 0;
            color: #4CAF50;
        }

        header p {
            font-size: 12px;
            color: #666;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .highlight {
            color: #4CAF50;
            font-weight: bold;
        }

        .barcode {
            margin-top: 10px;
            text-align: center;
        }

        .image {
            max-width: 120px;
            height: auto;
            display: block;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 5px;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .separator {
            margin-top: 10px;
            border-top: 1px dashed #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header dengan logo -->
    <header>
        <h1 class="super-title">SM-MART</h1>
        <h1>Receipt Barang Masuk</h1>
        <p>{{ now()->format('d F Y H:i') }}</p>
    </header>

    <!-- Menampilkan detail barang -->
    <table>
        <tr>
            <th>Barcode</th>
            <td class="highlight">{{ $order->barang->barcode }}</td>
        </tr>
        <tr>
            <th>Gambar Barang</th>
            <td>
                <img src="data:image/png;base64,{{ $imageBase64 }}" alt="Barang Image" class="image" height="100px">
            </td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td class="highlight">{{ $order->barang->name }}</td>
        </tr>
        <tr>
            <th>Supplier</th>
            <td>{{ $order->supplier->name }}</td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td class="highlight">{{ $order->quantity }}</td>
        </tr>
        <tr>
            <th>Tanggal Barang Masuk</th>
            <td>{{ \Carbon\Carbon::parse($order->date_received)->format('d F Y') }}</td>
        </tr>
        <tr>
            <th>Tanggal Kadaluarsa</th>
            <td>{{ \Carbon\Carbon::parse($order->expiration_date)->format('d F Y') }}</td>
        </tr>
    </table>

    <!-- Separator untuk tampilan yang rapi -->
    <div class="separator"></div>

    <!-- Footer dengan waktu cetak -->
    <div class="footer">
        Dicetak pada {{ now()->format('d F Y H:i') }} oleh {{ auth()->user()->name ?? 'Admin' }}
    </div>

</div>
</body>
</html>

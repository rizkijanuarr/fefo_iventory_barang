<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt Barang Keluar</title>
    <style>
        @page {
            size: 3.15in;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0.2in;
            width: auto;
            line-height: 1.2;
            font-size: 7pt;
            font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
        }

        table {
            width: 100%;
            table-layout: fixed;
        }

        th,
        td {
            padding: 2px 0;
            text-align: left;
        }

        .super-title {
            font-size: 5pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: -8px;
        }

        .title {
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: -5px;
        }

        .subtitle {
            font-size: 5pt;
            text-align: center;
            color: #71717a;
        }

        .footer {
            text-align: center;
            font-size: 5pt;
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
    <div>
        <h1 class="super-title">Alfamart</h1>
        <h2 class="title">Receipt Barang Keluar</h2>
        <p class="subtitle">Jl. Perjuangan No.11</p>

        <table>
            <thead>
                <tr>
                    <th style="width: 6%; font-size: 5pt;">#</th>
                    <th style="width: 60%; text-align: left; font-size: 5pt;">Nama Barang</th>
                    <th style="text-align: right;width: 40%; font-size: 5pt;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangKeluars as $index => $data)
                    <tr>
                        <th style="width: 6%; font-family: monospace">{{ $loop->iteration }}</th>
                        <td style="width: 60%; text-align: left; letter-spacing: -1.5px; font-family: 'monospace'">
                            {{ $data->barang->name }}</td>
                        <td style="width: 40%; text-align: right; letter-spacing: -1.5px; font-family: 'monospace'">
                            {{ $data->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Separator untuk tampilan yang rapi -->
        <div class="separator"></div>

        <!-- Tampilkan pagination -->
        <div class="footer">
            Halaman {{ $barangKeluars->currentPage() }} dari {{ $barangKeluars->lastPage() }}<br>
            Menampilkan {{ $barangKeluars->firstItem() }} - {{ $barangKeluars->lastItem() }} dari {{ $barangKeluars->total() }} barang keluar
        </div>

        <!-- Footer dengan waktu cetak -->
        <div class="footer">
            Dicetak pada {{ now()->format('d F Y H:i') }} oleh {{ auth()->user()->name ?? 'Admin' }}
        </div>
    </div>
</body>

</html>

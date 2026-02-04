<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Webhook WAHA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">

    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-5 border-b bg-gray-800 text-white flex justify-between items-center">
            <h1 class="text-xl font-bold">📢 Webhook Inbox Monitor</h1>
            <a href="/cek-webhook" class="px-4 py-2 bg-blue-500 rounded hover:bg-blue-600 text-sm">Refresh Halaman</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Payload (Isi Pesan)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i:s') }}
                                <br>
                                <span class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status == 'replied_success')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">✅ Terbalas</span>
                                @elseif($log->status == 'received')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">📥 Diterima (Belum Diproses)</span>
                                @elseif($log->status == 'unknown_number')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">⚠️ Nomor Asing</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <div class="max-h-40 overflow-y-auto bg-gray-50 p-2 rounded border">
                                    {{-- Coba ambil isi pesan --}}
                                    @php 
                                        $payload = is_array($log->payload) ? $log->payload : json_decode($log->payload, true);
                                        $body = $payload['payload']['body'] ?? '-';
                                        $from = $payload['payload']['from'] ?? '-';
                                    @endphp
                                    
                                    <strong>From:</strong> {{ $from }}<br>
                                    <strong>Pesan:</strong> {{ $body }}<br>
                                    <hr class="my-1 border-gray-300">
                                    <pre>{{ json_encode($payload, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-lg text-gray-400">
                                Belum ada data Webhook yang masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
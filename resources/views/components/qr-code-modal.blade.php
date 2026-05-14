<div class="text-center">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($office->survey_url) }}"
         alt="QR Code for {{ $office->name }}"
         class="mx-auto rounded-lg shadow-sm">

    <p class="mt-3 text-sm text-gray-500">
        Scan to open the survey for <strong>{{ $office->name }}</strong>
    </p>

    <div class="mt-4 flex justify-center gap-3">
        <a href="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($office->survey_url) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 hover:scale-105 hover:shadow-lg active:scale-95 transition-all duration-200">
            Download
        </a>
    </div>
</div>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tugas Operasional Outbound</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light pb-5">
    <div class="container my-5" style="max-width: 720px;">
        <div class="card border-0 shadow-sm p-4 rounded-3">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <h4 class="fw-bold m-0 text-dark">Buat Tugas Baru</h4>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Kembali</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger p-2 small">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.task.store') }}" method="POST">
                @csrf
                <input type="hidden" name="title" id="task_title" value="{{ old('title') }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold small">1. Kode Tugas</label>
                    <select name="task_code" id="task_code" class="form-select font-monospace" required
                        onchange="handleTaskCodeChange()">
                        <option value="" disabled {{ old('task_code') ? '' : 'selected' }}>-- Pilih Tugas --</option>
                        <option value="LOG-OPS-001" {{ old('task_code') == 'LOG-OPS-001' ? 'selected' : '' }}>
                            LOG-OPS-001 (Local)
                        </option>
                        <option value="LOG-OPS-002" {{ old('task_code') == 'LOG-OPS-002' ? 'selected' : '' }}>
                            LOG-OPS-002 (EXIM / Ekspor)
                        </option>
                        <option value="LOG-OPS-003" {{ old('task_code') == 'LOG-OPS-003' ? 'selected' : '' }}>
                            LOG-OPS-003 (Sparepart)
                        </option>
                        <option value="LOG-OPS-004" {{ old('task_code') == 'LOG-OPS-004' ? 'selected' : '' }}>
                            LOG-OPS-004 (Consumable)
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">2. Tanggal Penugasan</label>
                    <input type="date" name="task_date" id="task_date" class="form-control"
                        value="{{ old('task_date', date('Y-m-d')) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">3. Pilih Operator</label>
                    <select name="operator_id" class="form-select" required>
                        <option value="">-- Pilih Operator --</option>
                        @foreach ($operators as $op)
                            <option value="{{ $op->id }}" {{ old('operator_id') == $op->id ? 'selected' : '' }}>
                                {{ $op->username }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small text-muted">Panduan Instruksi Kerja Standar (WI /
                        SOP)</label>
                    <textarea name="description" id="work_instruction" class="form-control small bg-light" rows="3"
                        readonly>{{ old('description') }}</textarea>
                </div>

                <hr class="my-4 text-muted">
                <h6 class="fw-bold text-primary mb-3">Rincian Tugas</h6>

                <!-- 1. Task Name (Dropdown Dinamis) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold small">1. Task Name</label>
                    <select id="task_name_select" class="form-select" required onchange="handleTaskNameChange()">
                        <option value="" disabled selected>-- Pilih Kode Tugas Terlebih Dahulu --</option>
                    </select>
                </div>

                <!-- 2. Part Name (Dropdown Dinamis) -->
                <div class="mb-3" id="part_name_container">
                    <label class="form-label fw-semibold small">2. Part Name</label>
                    <select name="part_name" id="part_name_select" class="form-select" required
                        onchange="handlePartNameChange()">
                        <option value="" disabled selected>-- Pilih Kode Tugas Terlebih Dahulu --</option>
                    </select>
                </div>

                <!-- 3. Part Number (Disabled & Auto Fill) -->
                <div class="mb-3" id="part_number_container">
                    <label class="form-label fw-semibold small">3. Part Number</label>
                    <input type="text" id="part_number_display" class="form-control font-monospace bg-light"
                        placeholder="Otomatis terisi..." disabled>
                    <!-- Hidden input agar part_number tetap terkirim ke Controller -->
                    <input type="hidden" name="part_number" id="part_number" value="{{ old('part_number') }}">
                </div>

                <!-- 4. Quantity Per Box & 5. Total Quantity -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">4. Quantity (Pcs/Box)</label>
                        <input type="number" name="qty_per_box" class="form-control" placeholder="Contoh: 3" min="1"
                            value="{{ old('qty_per_box') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">5. Total Quantity (Pcs)</label>
                        <input type="number" name="total_qty" class="form-control" placeholder="Contoh: 300" min="1"
                            value="{{ old('total_qty') }}" required>
                    </div>
                </div>

                <!-- 6. Delivery Date & 7. Shipping Company -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6" id="delivery_date_container">
                        <label class="form-label fw-semibold small">6. Delivery Date</label>
                        <input type="date" name="delivery_date" id="delivery_date" class="form-control"
                            value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6" id="shipping_company_container">
                        <label class="form-label fw-semibold small">7. Shipping Company</label>
                        <input type="text" name="shipping_company" id="shipping_company" class="form-control"
                            placeholder="Contoh: PT. Mikuni Indonesia"
                            value="{{ old('shipping_company', 'PT. Mikuni Indonesia') }}" required>
                    </div>
                </div>

                <!-- 8. Company Destination -->
                <div class="mb-4">
                    <label class="form-label fw-semibold small">8. Company Destination (Customer)</label>
                    <input type="text" name="company_destination" id="company_destination" class="form-control"
                        placeholder="Contoh: PT. Suzuki Indomobil Motor" value="{{ old('company_destination') }}"
                        required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Simpan & Tugaskan Operator</button>
            </form>
        </div>
    </div>

    <script>
        const instructions = {
            "Prepare Delivery Local": "1. Keluarkan barang dari rak penyimpanan.\n2. Pasang dan beri stempel pada label setiap box.\n3. Pasang strapping band barang.\n4. Bungkus barang dengan plastic wrap.",
            "Prepare Delivery EXIM": "1. Keluarkan barang dari rak penyimpanan.\n2. Pasang stiker nomor per karton.\n3. Pasang strapping band karton barang.\n4. Bungkus karton barang bagian atas dengan lembar plastik.\n5. Bungkus karton barang beserta siku dengan plastic wrap.",
            "Packing Delivery": "Proses packing komponen sparepart sesuai standar proteksi packing part presisi.",
            "Prepare Empty Box": "Menyiapkan box kosong yang bersih dan siap digunakan untuk line produksi.",
            "Prepare Carton Box": "Menyiapkan dan merakit karton box untuk kebutuhan packing part consumable.",
            "Repair Holder Box": "Memeriksa dan memperbaiki holder box yang rusak atau tidak presisi.",
            "Cleaning Box": "Membersihkan box dari kotoran atau sisa oli sebelum digunakan kembali."
        };

        const taskNamesByCode = {
            "LOG-OPS-001": [
                { value: "Prepare Delivery Local", label: "Prepare Delivery" }
            ],
            "LOG-OPS-002": [
                { value: "Prepare Delivery EXIM", label: "Prepare Delivery" }
            ],
            "LOG-OPS-003": [
                { value: "Packing Delivery", label: "Packing Delivery" }
            ],
            "LOG-OPS-004": [
                { value: "Prepare Empty Box", label: "Prepare Empty Box" },
                { value: "Prepare Carton Box", label: "Prepare Carton Box" },
                { value: "Repair Holder Box", label: "Repair Holder Box" },
                { value: "Cleaning Box", label: "Cleaning Box" }
            ]
        };

        const partData = {
            local: [
                { group: "MMKI Local", name: "Intake Manifold (IMF 94)", pn: "14003W000P", dest: "PT. Mitsubishi Motors Krama Yudha Indonesia" },
                { group: "MMKI Local", name: "Intake Manifold (IMF 82)", pn: "1540A754", dest: "PT. Mitsubishi Motors Krama Yudha Indonesia" },
                { group: "SIM Local", name: "Intake Manifold (IMF 110)", pn: "13110-74U00", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Local", name: "Intake Manifold (IMF 79)", pn: "13110-52S00", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Local", name: "Intake Manifold (IMF 71)", pn: "13110-73R00", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Local", name: "Intake Manifold (IMF 63)", pn: "13110-67LB0", dest: "PT. Suzuki Indomobil Motor" },
                { group: "Yamaha Local", name: "Karburator packing/prepare", pn: "5TP-E4101-20", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Local", name: "Air Induction System (AIS) Valve packing/prepare", pn: "5YP-E4840-00", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Local", name: "Solenoid Coil packing/prepare", pn: "BK6-H6110-10", dest: "PT. Yamaha Indonesia Motor Mfg" }
            ],
            sparepart: [
                { group: "SIM Sparepart", name: "Throttle Body (THB) - 13400-39JC2", pn: "13400-39JC2", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Throttle Body (THB) - 13400-49K01", pn: "13400-49K01", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Throttle Body (THB) - 13400-12K01", pn: "13400-12K01", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Throttle Body (THB) - 13400-23K01", pn: "13400-23K01", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Karburator", pn: "13200-21DD1", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Oil Control Valve (OCV)", pn: "16550-58MA0", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Accelerator Pedal Module (APM)", pn: "49400-73R00", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Jet Pilot", pn: "09492-15011", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Diaphragm Assy", pn: "13480-09G10", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Oil Pump Assy", pn: "16400-09G20-000", dest: "PT. Suzuki Indomobil Motor" },
                { group: "SIM Sparepart", name: "Spring / Per Skep", pn: "13573-46G00", dest: "PT. Suzuki Indomobil Motor" },
                { group: "Yamaha Sparepart", name: "Karburator packing - 5FU-14101-12-00", pn: "5FU-14101-12-00", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Sparepart", name: "Karburator packing - 3KA-E4101-20y", pn: "3KA-E4101-20y", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Sparepart", name: "Karburator packing/prepare - 5TP-E4101-20", pn: "5TP-E4101-20", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Sparepart", name: "Air Induction System (AIS) Valve packing/prepare", pn: "5YP-E4840-00", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Sparepart", name: "Stepper Motor packing", pn: "2DPH58010000", dest: "PT. Yamaha Indonesia Motor Mfg" },
                { group: "Yamaha Sparepart", name: "Solenoid Coil packing/prepare", pn: "BK6-H6110-10", dest: "PT. Yamaha Indonesia Motor Mfg" }
            ]
        };

        function handleTaskCodeChange() {
            const taskCode = document.getElementById('task_code').value;
            const taskNameSelect = document.getElementById('task_name_select');
            const partSelect = document.getElementById('part_name_select');
            const deliveryDateInput = document.getElementById('delivery_date');
            const shippingCompInput = document.getElementById('shipping_company');
            const partNumDisplay = document.getElementById('part_number_display');
            const partNumHidden = document.getElementById('part_number');
            const destInput = document.getElementById('company_destination');

            // 1. Populasi Task Name sesuai Kode Tugas
            taskNameSelect.innerHTML = '<option value="" disabled selected>-- Pilih Task Name --</option>';
            const taskList = taskNamesByCode[taskCode] || [];
            taskList.forEach((t, index) => {
                const opt = document.createElement('option');
                opt.value = t.value;
                opt.text = t.label;
                taskNameSelect.appendChild(opt);
            });

            // Jika hanya 1 pilihan, langsung pilih otomatis
            if (taskList.length === 1) {
                taskNameSelect.selectedIndex = 1;
                handleTaskNameChange();
            } else {
                document.getElementById('task_title').value = '';
                document.getElementById('work_instruction').value = '';
            }

            // 2. Logika Khusus per Kode Tugas
            if (taskCode === "LOG-OPS-004") {
                // CONSUMABLE: Jangan disable, tapi kunci dengan CSS dan isi nilai default agar terkirim ke Laravel
                partSelect.innerHTML = '<option value="-" selected>- (Bukan Barang Hasil Produksi) -</option>';
                partSelect.style.pointerEvents = 'none'; // Mematikan klik
                partSelect.classList.add('bg-light');

                partNumDisplay.value = '-';
                partNumHidden.value = '-';

                // Ambil tanggal hari ini atau dari kolom task_date agar tidak error format tanggal
                deliveryDateInput.value = document.getElementById('task_date').value || new Date().toISOString().split('T')[0];
                deliveryDateInput.readOnly = true;
                deliveryDateInput.classList.add('bg-light');

                shippingCompInput.value = '-';
                shippingCompInput.readOnly = true;
                shippingCompInput.classList.add('bg-light');

                destInput.value = 'Internal Plant / Line Produksi';
                destInput.readOnly = true;
                destInput.classList.add('bg-light');
            } else {
                // Non-Consumable: Kembalikan input ke kondisi bisa diedit
                partSelect.style.pointerEvents = 'auto'; // Mengembalikan klik
                partSelect.classList.remove('bg-light');

                deliveryDateInput.readOnly = false;
                deliveryDateInput.classList.remove('bg-light');
                if (!deliveryDateInput.value || deliveryDateInput.value === '-') {
                    deliveryDateInput.value = new Date().toISOString().split('T')[0];
                }

                shippingCompInput.readOnly = false;
                shippingCompInput.classList.remove('bg-light');
                if (!shippingCompInput.value || shippingCompInput.value === '-') {
                    shippingCompInput.value = 'PT. Mikuni Indonesia';
                }

                destInput.readOnly = false;
                destInput.classList.remove('bg-light');
                destInput.value = '';

                partNumDisplay.value = '';
                partNumHidden.value = '';

                partSelect.innerHTML = '<option value="" disabled selected>-- Pilih Part Name --</option>';

                if (taskCode === "LOG-OPS-001") {
                    populatePartOptions(partData.local, partSelect);
                } else if (taskCode === "LOG-OPS-003") {
                    populatePartOptions(partData.sparepart, partSelect);
                } else if (taskCode === "LOG-OPS-002") {
                    partSelect.innerHTML = '<option value="" disabled selected>-- Part Ekspor Belum Dikonfigurasi --</option>';
                }
            }
        }

        function handleTaskNameChange() {
            const taskNameSelect = document.getElementById('task_name_select');
            const selectedVal = taskNameSelect.value;
            const taskTitleInput = document.getElementById('task_title');
            const instructionArea = document.getElementById('work_instruction');

            taskTitleInput.value = selectedVal;
            instructionArea.value = instructions[selectedVal] || '';
        }

        function populatePartOptions(items, targetSelect) {
            const groups = {};

            items.forEach(item => {
                if (!groups[item.group]) {
                    groups[item.group] = [];
                }
                groups[item.group].push(item);
            });

            for (const groupName in groups) {
                const optGroup = document.createElement('optgroup');
                optGroup.label = groupName;

                groups[groupName].forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.name;
                    option.text = `${item.name} (${item.pn})`;
                    option.setAttribute('data-pn', item.pn);
                    option.setAttribute('data-dest', item.dest);
                    optGroup.appendChild(option);
                });

                targetSelect.appendChild(optGroup);
            }
        }

        function handlePartNameChange() {
            const select = document.getElementById('part_name_select');
            const selectedOption = select.options[select.selectedIndex];

            if (selectedOption && selectedOption.value && selectedOption.value !== '-') {
                const pn = selectedOption.getAttribute('data-pn');
                const dest = selectedOption.getAttribute('data-dest');

                document.getElementById('part_number_display').value = pn;
                document.getElementById('part_number').value = pn;
                document.getElementById('company_destination').value = dest;
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('task_code').value) {
                handleTaskCodeChange();
            }
        });
    </script>
</body>

</html>
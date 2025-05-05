<script setup lang="ts">
import { onMounted, ref, reactive } from 'vue';
import axios from 'axios';
import echo from '@/echo';

interface Antrian {
    id: number;
    nomor_antrian: string;
    status: string;
    loket_id: number | null;
}

interface Loket {
    id: number;
    nama: string;
    aktif: boolean;
}

const antrianMenunggu = ref<Antrian[]>([]);
const antrianAktif = reactive<Record<number, Antrian | null>>({});
const lokets = ref<Loket[]>([]);
const isLoading = ref(true);

const nomorAntrian = ref<string | null>(null);
const loading = ref(false);

const ambilAntrian = async () => {
    loading.value = true;
    try {
        const response = await axios.post('/api/antrian');
        const antrianBaru = response.data;
        nomorAntrian.value = antrianBaru.nomor_antrian;
        antrianMenunggu.value.push(antrianBaru);
    } catch (error) {
        console.error('Gagal mengambil antrian:', error);
    } finally {
        loading.value = false;
    }
};

const loadData = async () => {
    try {
        const [antrianRes, loketRes] = await Promise.all([
            axios.get('/api/antrian'),
            axios.get('/api/loket')
        ]);

        antrianMenunggu.value = antrianRes.data.filter((a: Antrian) => a.status === 'menunggu');
        lokets.value = loketRes.data;

        lokets.value.forEach((loket: Loket) => {
            antrianAktif[loket.id] = null;
        });

        antrianRes.data
            .filter((a: Antrian) => a.status === 'dipanggil')
            .forEach((antrian: Antrian) => {
                if (antrian.loket_id) {
                    antrianAktif[antrian.loket_id] = antrian;
                }
            });
    } catch (error) {
        console.error('Error loading data:', error);
    } finally {
        isLoading.value = false;
    }
};

const panggilAntrian = async (loketId: number) => {
    try {
        const response = await axios.post(`/api/antrian/panggil/${loketId}`);
        const { antrian, loket } = response.data;
        sebutkanAntrian(antrian.nomor_antrian, loket.nama);
    } catch (error) {
        console.error('Error calling queue:', error);
    }
};

const sebutkanAntrian = (nomor: string, loket: string) => {
    const text = `Nomor antrian ${nomor.split('').join(' ')}, silakan menuju loket ${loket}`;
    console.log('🗣️ Mengucapkan:', text);
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'id-ID';
        utterance.rate = 0.8;
        speechSynthesis.speak(utterance);
    } else {
        console.warn('Speech synthesis not supported in this browser');
    }
};

onMounted(() => {
    loadData();

    echo.channel('antrian-channel')
        .listen('AntrianDipanggil', (data: { antrian: Antrian, loket: Loket }) => {
            antrianAktif[data.loket.id] = data.antrian;
            antrianMenunggu.value = antrianMenunggu.value.filter(a => a.id !== data.antrian.id);
        })
        .listen('AntrianDiambil', (data: { antrian: Antrian }) => {
            antrianMenunggu.value.push(data.antrian);
        })
        .error((err: any) => {
            console.error('Error di Echo channel:', err);
        });

    echo.connector.pusher.connection.bind('state_change', (state: any) => {
        console.log('Pusher state change:', state);
    });

    echo.connector.pusher.connection.bind('connected', () => {
        console.log('Connected to Pusher');
    });

    echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('Disconnected from Pusher');
    });
});
</script>

<template>
    <div class="antrian-container">
        <div class="antrian-form">
            <button @click="ambilAntrian" :disabled="loading">
                {{ loading ? 'Memproses...' : 'Ambil Antrian' }}
            </button>
            <div v-if="nomorAntrian" class="nomor-antrian">
                Nomor Antrian Anda: <strong>{{ nomorAntrian }}</strong>
            </div>
        </div>

        <div v-if="isLoading" class="loading">
            Memuat data...
        </div>
        <div v-else>
            <div class="antrian-waiting">
                <h2>Antrian Menunggu</h2>
                <div class="antrian-list">
                    <div v-for="antrian in antrianMenunggu" :key="antrian.id" class="antrian-item">
                        {{ antrian.nomor_antrian }}
                    </div>
                    <div v-if="antrianMenunggu.length === 0" class="no-data">
                        Tidak ada antrian menunggu
                    </div>
                </div>
            </div>

            <div class="loket-container">
                <div v-for="loket in lokets" :key="loket.id" class="loket">
                    <h3>Loket {{ loket.nama }}</h3>
                    <div class="antrian-aktif" :class="{ empty: !antrianAktif[loket.id] }">
                        {{ antrianAktif[loket.id]?.nomor_antrian || 'Tidak ada antrian' }}
                    </div>
                    <div class="loket-controls">
                        <button @click="panggilAntrian(loket.id)" :disabled="antrianMenunggu.length === 0"
                            class="btn btn-primary">
                            Panggil Antrian
                        </button>
                        <button v-if="antrianAktif[loket.id]"
                            @click="sebutkanAntrian(antrianAktif[loket.id]?.nomor_antrian || '', loket.nama)"
                            class="btn btn-secondary">
                            Panggil Ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.antrian-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.antrian-form {
    margin-bottom: 2rem;
}

.nomor-antrian {
    margin-top: 1rem;
    font-size: 1.2rem;
}

.loading {
    text-align: center;
    padding: 2rem;
    font-size: 1.2rem;
}

.antrian-waiting {
    margin-bottom: 2rem;
}

.antrian-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.antrian-item {
    padding: 10px 15px;
    background-color: #f0f0f0;
    border-radius: 4px;
    font-weight: bold;
}

.loket-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.loket {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.loket h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.antrian-aktif {
    font-size: 2rem;
    font-weight: bold;
    text-align: center;
    margin: 15px 0;
    padding: 15px;
    background-color: #e6f7ff;
    border-radius: 4px;
}

.antrian-aktif.empty {
    background-color: #f5f5f5;
    color: #999;
    font-size: 1rem;
}

.loket-controls {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 500;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-primary {
    background-color: #1890ff;
    color: white;
}

.btn-secondary {
    background-color: #52c41a;
    color: white;
}

.no-data {
    padding: 10px;
    color: #999;
    font-style: italic;
}
</style>

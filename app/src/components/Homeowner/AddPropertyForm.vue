<template>
    <v-container>
        <v-alert v-if="message.text" class="mb-5 text-white" :color="message.type">
          {{ message.text }}
        </v-alert>

        <v-form ref="form" v-model="valid" lazy-validation>
            <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="6">
                    <v-text-field v-model="property.title" :rules="titleRules" label="Nom du bien" required ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="property.price" type="number" :rules="monthlyRentRules" label="Prix mensuel" required ></v-text-field>
                </v-col>
            </v-row>
             <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="6">
                    <v-select v-model="property.type" :items="propertyType" :rules="typeRules" label="Type de bien" solo></v-select>
                </v-col>
                <v-col cols="12" md="6">
                    <v-select :items="numberRoom" label="Nombre de chambres" solo v-model="property.number_rooms"></v-select>
                </v-col>
            </v-row>
            <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="12">
                    <v-text-field type="number" v-model="property.surface" :rules="surfaceRules" label="Surface" required></v-text-field>
                </v-col>
            </v-row>
            <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="3">
                    <v-text-field v-model="property.address" :rules="addressRules" label="Adresse" required></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                    <v-text-field v-model="property.zipcode" :rules="zipcodeRules" label="Code postal" required></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                    <v-text-field v-model="property.city" :rules="zipcodeRules" label="Ville" required></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                    <v-select :items="countryItems"  solo v-model="property.country" label="Pays" required></v-select>
                </v-col>
            </v-row>
           <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="12">
                    <v-textarea outlined name="input-7-4" label="Description" v-model="property.description"></v-textarea>
                </v-col>
            </v-row> 
            <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.has_balcony" label="Balcon"></v-checkbox>
                </v-col>
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.has_terrace" label="Terasse"></v-checkbox>
                </v-col>
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.has_cave" label="Cave"></v-checkbox>
                </v-col>
            </v-row>

            <v-row class="d-flex justify-center align-start">
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.has_elevator" label="Ascenseur"></v-checkbox>
                </v-col>
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.has_parking" label="Parking"></v-checkbox>
                </v-col>
                <v-col cols="12" md="4">
                    <v-checkbox  v-model="property.is_furnished" label="Equipé"></v-checkbox>
                </v-col>
            </v-row> 
            <v-col cols="12" class="d-flex justify-center">
                <v-progress-circular v-if="loading" indeterminate color="primary"></v-progress-circular>
                <v-btn v-else color="primary" class="mr-4" @click="addProperty"> Ajouter ce bien </v-btn>
            </v-col>
        </v-form>
    </v-container>
</template>

<script setup lang="ts">
    import { useRoute, useRouter } from "vue-router";
    import { axios } from '@/services/auth';
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/auth.store';
    import { PropertyEnum } from "@/enums/PropertyEnum";
    import { Roles } from "@/enums/roles";

    const router = useRouter();
    const route = useRoute();

    const authStore = useAuthStore();
    const form = ref();

    interface Property {
        title: string,
        address: string,
        zipcode: string,
        city: string,
        country: string,
        description: string,
        photos: [],
        type: string, 
        number_rooms: number,
        surface: any,
        has_balcony: boolean
        has_terrace: boolean
        has_cave: boolean
        has_elevator: boolean
        has_parking: boolean
        is_furnished: boolean
        price: any
        state: string
    }

    const property = reactive<Property>({
        title: '',
        address: '',
        zipcode: '',
        city: '',
        country: '',
        description: '',
        photos: [],
        type: '',
        number_rooms: 0,
        surface: 0,
        has_balcony: false,
        has_terrace: false,
        has_cave: false,
        has_elevator: false,
        has_parking: false,
        is_furnished: false,
        price: 0,
        state: PropertyEnum.Availaible,
    });
    

    const propertyType: string[] = ['Appartement', 'Maison'];
    const numberRoom = [0, 1, 2, 3, 4, 5];
    const countryItems: string[] = ['France', 'Belgique', 'Luxembourg']

    const valid = ref(false);
    const loading = ref(false);

    const user = await authStore.getUser;

    const errorType = ref('');
    const message = ref({
        text: '',
        type: ''
    })

    const titleRules = ref([
        (v: string) => !!v || 'Le nom du bien est requis',
    ]);

    const monthlyRentRules = ref([
        (v: string) => !!v || 'Le prix du bien est requis',
    ]);

    const typeRules = ref([
        (v: string) => !!v || 'Le type du bien est requis',
    ]);

    const stateRules = ref([
        (v: string) => !!v || 'L\'état du bien est requis',
    ]);

    const addressRules = ref([
        (v: string) => !!v || 'L\'adresse  du bien est requis',
    ]);

    const zipcodeRules = ref([
        (v: string) => !!v || 'Le code postal du bien est requis',
    ]);

    const countryRules = ref([
        (v: string) => !!v || 'Le pays du bien est requis',
    ]);

    const numberRoomRules = ref([
        (v: string) => !!v || 'Le nombre de chambre du bien est requis',
    ]);


    const surfaceRules = ref([
        (v: number) => !!v || 'La surface du bien est requis',
    ]);

    const addProperty = async (event: MouseEvent) => {
        event.preventDefault();
        loading.value = true;
        if(valid.value) {
            const data = {
                title: property.title,
                address: property.address,
                zipcode: property.zipcode,
                city: property.city,
                country: property.country,
                description: property.description,
                photos: property.photos,
                type: property.type, 
                number_rooms: property.number_rooms,
                surface: parseInt(property.surface),
                has_balcony: property.has_balcony,
                has_terrace: property.has_terrace,
                has_cave: property.has_cave,
                has_elevator: property.has_elevator,
                has_parking: property.has_parking,
                is_furnished: property.is_furnished,
                price: parseInt(property.price),
                state: property.state,
                owner: `users/${user.id}`

            }

            message.value.text = '';
            message.value.type = '';

            try {
                const response = await axios.post(`${import.meta.env.VITE_BASE_API_URL}/properties`, data);
                console.log(response);
                message.value.text = 'Votre bien a été ajouté avec succès';
                message.value.type = 'info';
                router.push({ name: `${Roles.Homeowner}_property_add_photos`, params: { id: response.data.id } })
            } catch(error: any) {
                console.log(error);
                errorType.value = error.response.data.error_type || '';
                message.value.text = error.response.data.detail || 'Une erreur est survenue. Veuillez réessayer.';
                message.value.type = 'error';
            }
        } else {
            form.value.validate();
        }
        loading.value = false;
    }
    
</script>

<style scoped>
</style>
<script setup lang="ts">

import {ref} from "vue";
import {axios} from "@/services/auth";
import { useAuthStore } from "@/stores/auth.store";
import { Roles } from "@/enums/roles";
import ImagesSlider from "@/components/ImagesSlider.vue";
import { UserValidationStatus } from "@/enums/UserValidationStatus";
import { getFullUrl } from "@/helpers/assets";

const props = defineProps({
      id: String,
    });

const message = ref({ text: '', type: undefined as "error" | "success" | "warning" | "info" | undefined });
const loading = ref(false);
const authStore = useAuthStore();
const user = await authStore.getUser;
const alreadyRequested = ref(false);

async function getMyProperty() {
  try {
    if(user.role !== Roles.Tenant)
    {
      const response = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/properties/${props.id}`);
      return response.data;

    }else {
      const response = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/property_details/by_tenant/${props.id}`);
      alreadyRequested.value = response.data.isAlreadyApplied;
      return response.data;
    }
  } catch (error: any) {
    console.log("err: ", error)
    message.value.text = error.response.data.detail || 'Une erreur est survenue. Veuillez réessayer.';
    message.value.type = 'error';
  }
}
const property = await getMyProperty();

const createRequest = async () => {
  if(user.role !== Roles.Tenant){
    message.value.text = 'Vous n\'êtes pas locataire.';
    message.value.type = 'error';
  }else {
    loading.value = true;
    try {
      const response = await axios.post(`${import.meta.env.VITE_BASE_API_URL}/property_request/by_tenant`, {
        property: `/properties/${props.id}`
      });
      console.log(response);
      alreadyRequested.value = true;
      message.value.text = 'Votre demande a été envoyée avec succès.';
      message.value.type = 'success';
    } catch (error: any) {
      console.log("err: ", error)
      message.value.text = error.response.data.detail || 'Une erreur est survenue. Veuillez réessayer.';
      message.value.type = 'error';
    }
    loading.value = false;
  }
}
</script>

<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-alert v-if="message.text" :type="message.type" class="mb-5" dismissible>{{message.text}}</v-alert>
        <ImagesSlider :images="property?.photosUrl"></ImagesSlider>
      </v-col>
      <v-col cols="12">
        <v-card class="h-100">
          <div id="details-area" class=" w-100">
            <div class="d-flex justify-between">
              <div>
                <v-card-title class="d-flex align-center font-weight-bold">
                  <span class="text-h4">{{property?.price}}</span><v-icon icon="mdi mdi-currency-eur"></v-icon>
                </v-card-title>
                <v-card-subtitle class="text-h5 font-weight-bold text-primary mb-2 py-2">
                  {{property?.title}}
                </v-card-subtitle>
                <v-card-subtitle class="text-h6 mb-2">
                  {{property?.address}}
                </v-card-subtitle>
                <v-card-subtitle class="mb-2 d-flex items-center">
                  <v-icon v-if="property?.type === 'House'" icon="mdi mdi-home-outline mr-2"></v-icon>
                  <v-icon v-if="property?.type === 'Apartment'" icon="mdi mdi-office-building-outline mr-2"></v-icon>
                  <span>{{property?.type}}</span>
                </v-card-subtitle>
              </div>
              <v-progress-circular v-if="loading" indeterminate  color="primary" class="mr-5 mt-5 ml-auto"></v-progress-circular>
              <v-alert v-if="alreadyRequested" type="info" variant="tonal" class="ma-5" dismissible>Vous avez déjà fait une requête pour ce bien</v-alert>
              <v-btn v-else-if="!loading && user.role === Roles.Tenant && user.validationStatus === UserValidationStatus.Validated" class="mr-5 mt-5 ml-auto" color="primary" @click="createRequest">Postuler</v-btn>
            </div>
            <v-card-text>
              <v-list>
                <v-list-item class="text-h7">{{property?.rooms}} pièces<v-icon icon="mdi mdi-floor-plan ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7">{{property?.surface}} &#13217; <v-icon icon="mdi mdi-tape-measure ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7" v-if="property?.state"> {{property?.state}}<v-icon icon="mdi mdi-door-closed-lock ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7" v-if="property?.has_elevator">Ascenseur <v-icon icon="mdi mdi-elevator ml-2"></v-icon></v-list-item>
                  <v-list-item class="text-h7" v-if="property?.has_balcony">Balcon <v-icon icon="mdi mdi-balcony ml-2"></v-icon></v-list-item>
                  <v-list-item class="text-h7" v-if="property?.has_parking">Parking <v-icon icon="mdi mdi-garage ml-2"></v-icon></v-list-item>
                  <v-list-item class="text-h7" v-if="property?.has_terrace">Terrasse <v-icon icon="mdi mdi-fence ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7" v-if="property?.is_furnished">Meublé <v-icon icon="mdi mdi-sofa-single-outline ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7" v-if="property?.has_cave">Cave <v-icon icon="mdi mdi-door-closed-lock ml-2"></v-icon></v-list-item>
                <v-list-item class="text-h7">{{property?.address}} ({{property?.zipcode}}), {{property?.city}} {{property?.country}}</v-list-item>
              </v-list> 
            </v-card-text>
            <v-card-text class="px-5">
              <h1 class="text-h5 font-weight-bold heading-sentence">
                <span>Description</span>
              </h1>
              {{property?.description}}
            </v-card-text>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
#container{
  display: grid;
  grid-template-columns: 3fr 2fr;
  gap: 30px;
}
</style>
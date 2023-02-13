<script setup lang="ts">

import {ref} from "vue";
import {axios} from "@/services/auth";
import { useAuthStore } from "@/stores/auth.store";
import { Roles } from "@/enums/roles";

const props = defineProps({
      id:String,
    });

const message = ref({
  text:'',
  type:''
});

const role = useAuthStore().getRole;

async function getPhotoLink(id:String) {
  try {
    const response = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/media_objects/${id}`);
    const photo_link = await response.data.filePath
    return await photo_link;

  } catch (error: any) {
    console.log("err: ", error)
    message.value.text = '';
    message.value.type = '';
    message.value.text = error.response.data.message || 'Une erreur est survenue. Veuillez réessayer.';
    message.value.type = 'error';
  }
}

async function getMyProperty(id:any) {
  try {

    const response = await axios.get(`${import.meta.env.VITE_BASE_API_URL}/properties/${props.id}`);
    const photos_links = [];
    for (const photo_id of response.data.photos) {
      const link  = await getPhotoLink(photo_id.split('/').pop())
      photos_links.push(link)
    }
      response.data.photos = photos_links;
    return response.data;

  } catch (error: any) {
    console.log("err: ", error)
    message.value.text = '';
    message.value.type = '';
    message.value.text = error.response.data.message || 'Une erreur est survenue. Veuillez réessayer.';
    message.value.type = 'error';
  }
}
const property = await getMyProperty(props.id);

</script>

<template>
  <div id="container">
    <div>
      <v-carousel id="images_slider">
        <v-carousel-item
            v-for="(link,i) in property.photos"
            :key="i"
            :src="link"
            cover
        ></v-carousel-item>
      </v-carousel>
    </div>
    <v-card class="h-100">
      <div id="details-area" class=" w-100">
        <div class="d-flex justify-between">
          <div>
            <v-card-title class="text-h4 font-weight-bold">
              <v-icon icon="mdi mdi-currency-eur"></v-icon>
                {{property.price}}
            </v-card-title>
            <v-card-subtitle class="text-h5 font-weight-bold text-primary mb-2 py-2">
              {{property.title}}
            </v-card-subtitle>
            <v-card-subtitle class="text-h6 mb-2">
              {{property.address}}
            </v-card-subtitle>
            <v-card-subtitle class="mb-2 d-flex items-center">
              <v-icon v-if="property.type === 'House'" icon="mdi mdi-home-outline mr-2"></v-icon>
              <v-icon v-if="property.type === 'Apartment'" icon="mdi mdi-office-building-outline mr-2"></v-icon>
              <span>{{property.type}}</span>
            </v-card-subtitle>
          </div>
          <v-btn v-if="role === Roles.Tenant" class="mr-5 mt-5 ml-auto" color="primary">Postuler</v-btn>
        </div>
        <v-card-text>
          <v-list>
            <v-list-item class="text-h7">{{property.rooms}} pièces<v-icon icon="mdi mdi-floor-plan ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7">{{property.surface}} &#13217; <v-icon icon="mdi mdi-tape-measure ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7" v-if="property.state"> {{property.state}}<v-icon icon="mdi mdi-door-closed-lock ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7" v-if="property.has_elevator">Ascenseur <v-icon icon="mdi mdi-elevator ml-2"></v-icon></v-list-item>
              <v-list-item class="text-h7" v-if="property.has_balcony">Balcon <v-icon icon="mdi mdi-balcony ml-2"></v-icon></v-list-item>
              <v-list-item class="text-h7" v-if="property.has_parking">Parking <v-icon icon="mdi mdi-garage ml-2"></v-icon></v-list-item>
              <v-list-item class="text-h7" v-if="property.has_terrace">Terrasse <v-icon icon="mdi mdi-fence ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7" v-if="property.is_furnished">Meublé <v-icon icon="mdi mdi-sofa-single-outline ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7" v-if="property.has_cave">Cave <v-icon icon="mdi mdi-door-closed-lock ml-2"></v-icon></v-list-item>
            <v-list-item class="text-h7">{{property.address}} ({{property.zipcode}}), {{property.city}} {{property.country}}</v-list-item>
          </v-list> 
        </v-card-text>
        <div>
        </div>
      </div>
    </v-card>
  </div>
  <v-card-text class="mt-10">
    {{property.description}}
  </v-card-text>
</template>

<style scoped>
#container{
  display: grid;
  grid-template-columns: 3fr 2fr;
  gap: 30px;
}
</style>
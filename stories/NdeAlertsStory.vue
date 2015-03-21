<template>
    <div>
        <nde-alert color="blue" text>
            <v-row>
                <v-col cols="12" md="1">
                    <span class="grey--text">File #:</span>
                </v-col>
                <v-col cols="12" md="11">
                    <span class="black--text">5226</span>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="1">
                    <span class="grey--text">Records:</span>
                </v-col>
                <v-col cols="12" md="11">
                    <span class="black--text">46</span>
                </v-col>
            </v-row>
        </nde-alert>
        
        <p class="red--text text--darken-3 font-italic">If you log intro Energy Depot using this address, you will see orders made this way.</p>

        <nde-alert text type="error" :messages="messages"></nde-alert>

        <nde-alert isDefault :messages="messages"></nde-alert>

        <nde-alert color="red lighten-5" class="text-center">
            <span class="red--text text--darken-3">Are you sure you want to delete this record?</span>
        </nde-alert>

        <nde-alert isDefault :messages="messages1">
            
        </nde-alert>
    </div>

   
</template>

<script>
import NdeAlert from '../resources/js/components/Alert/NdeAlert.vue';

export default {
    name: 'nde-alerts-story',
    components: {NdeAlert},
    data() {
        return {
            messages: [
                'Aliquam, pharetra fermentum, turpis egestas. Eget ut nunc aenean enim, fermentum fringilla eget. Volutpat nunc tincidunt malesuada sed ultrices blandit volutpat elementum. Habitasse lacus dolor pretium gravida vel scelerisque amet metus, mattis. Arcu ut euismod pellentesque blandit. Tempus in sed malesuada purus dictum donec viverra nisi. Aliquam felis morbi pellentesque commodo id. Elit ullamcorper arcu fames sed eget eget posuere posuere ante.',
                'Aliquam, pharetra fermentum, turpis egestas. Eget ut nunc aenean enim, fermentum fringilla eget. Volutpat nunc tincidunt malesuada sed ultrices blandit volutpat elementum. Habitasse lacus dolor pretium gravida vel scelerisque amet metus, mattis. Arcu ut euismod pellentesque blandit. Tempus in sed malesuada purus dictum donec viverra nisi. Aliquam felis morbi pellentesque commodo id. Elit ullamcorper arcu fames sed eget eget posuere posuere ante.'
            ],
            messages1: [
                'Fill out at least one field marked with  Δ',
                'Fill out at least one field marked with  ΔΔ '
            ]
        }
    }
}
</script>


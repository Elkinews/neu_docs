<template>
    <v-app-bar class="white nde-header" :flat="true">
        <a class="neudocs-masthead-logo"><img alt="NeuDocs Enterprise Logo" src="images/neubus_logo_masthead.png"></a>
        <v-menu v-if="type !== 'login'" offset-y>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    color="primary"
                    dark
                    v-bind="attrs"
                    v-on="on">
                    Programs
                </v-btn>
            </template>
            <v-list>
                <div v-for="(program, i) in programs" :key="i">
                    <v-list-item>
                        <v-list-item-title v-text="program.name"/>
                    </v-list-item>
                </div>
            </v-list>
        </v-menu>
    </v-app-bar>
</template>

<style scoped lang="scss">
    .nde-header{
        max-height: 64px;
    }
    .nde-logo{
        height: 51px;
        float:left !important;
    }
</style>

<script>
export default {
    data() {
        return {
            programs: [],
        };
    },
    mounted() {
        if (this.type === "dashboard"){
            console.log("xxx " + this.programsJson);
            this.programs = JSON.parse(this.programsJson);
        }
    },
    methods: {

    },
    props: {
        type: {
            type: String,
            required: false,
            default: ""
        },
        programsJson: {
            type: String,
            required: false,
            default: ""
        }
    },
    components: {

    }

}
</script>

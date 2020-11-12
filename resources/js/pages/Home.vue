<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">
                DESEVEN
            </div>
            <div class="card-body">
                <p>
                   Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quibusdam ullam nostrum quam odit repudiandae odio aut non impedit error possimus quas ea nemo ratione aspernatur dolore, quidem amet molestiae corporis?.
                </p>
                <p>
                    Lorem ipsum dolor sit amet consectetur, adipisicing elit. Molestiae tenetur minus modi sapiente officia esse est maxime ut, excepturi totam, a ab nam in earum inventore, nihil id aut nostrum?.
                </p>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex'
export default {
    name: 'home',
    computed: {
        ...mapState(['isUserLoggedIn'])
    },
    methods: {
        dashboard() {
            this.$router.push({
                name: this.$store.getters.dashboard
            })
        }
    }
}
</script>
<template>
    <div id="main">
        <div class="container">
            <header id="header">
                <h1 class="mbt-15 text-center">
                    DESEVENS
                </h1>
                <navigationMenu></navigationMenu>
            </header>
        </div>
        <div id="content">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
import navigationMenu from './components/Menu.vue'
export default {
    data() {
        return {
            //
        };
    },
    components: {
      navigationMenu
    }
};
</script>
<style>
.mr-15 {
    margin-right: 15px;
}
.mbt-15 {
    margin: 15px auto;
}
p.error_message {
    margin-bottom: 0;
}
</style>

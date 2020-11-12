<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header"><strong>Inventory</strong></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm" :class="view == 'category' ? 'btn-warning' : 'btn-info'" @click.prevent="changeView('category')">Categories</button>
                        <button class="btn btn-sm" :class="view == 'product' ? 'btn-warning' : 'btn-info'" @click.prevent="changeView('product')">Products</button>
                    </div>
                </div>
                <category-block v-if="view == 'category'"></category-block>
                <product-block v-if="view == 'product'"></product-block>
            </div>
        </div>
    </div>
</template>
<script>
import CategoryBlock from '../../components/CategoryBlock.vue'
import ProductBlock from '../../components/ProductBlock.vue'
export default {
    name: 'Inventory',
    components: {
        CategoryBlock,
        ProductBlock
    },
    computed: {
    },
    mounted() {
    },
    data() {
        return {
            view: 'category'
        }
    },
    methods: {
        changeView(view) {
            this.view = view
        }
    }
};
</script>

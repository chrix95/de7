<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">
				Customer Dashboard
				<span class="pull-right">
					<router-link :to="{ name: 'cart' }">
						Cart ({{ this.$store.getters.cartCount }})
					</router-link>
				</span>
			</div>
            <div class="card-body">
				<div class="row text-center">
					<div class="col-md-2" v-for="(cat, index) in categories" :key="index">
						<router-link :to="{ name: 'category.view', params: { catId: cat.id } }" class="text-center">
							{{ cat.name }}
						</router-link>
					</div>
				</div>
				<div class="row mbt-15 text-center" v-if="!loading">
					<div class="col-md-3" v-for="(item, index) in inventory" :key="index">
						<img src="https://via.placeholder.com/150" class="img img-responsive center-block" alt="Placeholder">
						<h5 class="mt-15">{{ item.name }}</h5>
						<p>
							Remaining quantity: {{ item.quantity }} <br>
							NGN {{ $globalFunc.currency(item.price) }}
						</p>
						<button class="btn btn-info" type="button" @click.prevent="$store.dispatch('addToCart', item)">
							Add to Cart
						</button>
					</div>
				</div>
				<div class="row" v-else>
					<div class="col-md-12">
						<h4>Loading...</h4>
					</div>
				</div>
            </div>
        </div>
    </div>
</template>
<script>  
import { mapState } from 'vuex'
import CategoryService from "../../services/CategoryService"
import InventoryService from "../../services/InventoryService"
export default {
	name: 'CustomerDashboard',
	computed: {
		...mapState(['loading'])
	},
	mounted() {
		this.getCategories()
		this.getInventory()
	},
    data() {
      return {
        categories: [],
		inventory: [],
		error_message: ''
      }
    },
    methods: {
      	getCategories() {
            this.$store.dispatch("loading", true)
            CategoryService.getCategories()
                .then((result) => {
                    if (result.data.status) {
                        this.categories = result.data.data
                    } else {
                        this.error_message = result.data.message
                    }
                    this.$store.dispatch("loading", false)
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false)
                    if (err.response.status === 401) {
                        this.$store.dispatch("logout")
                        this.$router.push({
                            name: 'login'
                        });
                    }
                    if (err.response === undefined) {
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.error_message = err.response.data.message
                    }
                })
		},
		getInventory() {
            this.$store.dispatch("loading", true)
            InventoryService.getInventory()
                .then((result) => {
                    if (result.data.status) {
                        this.inventory = result.data.data
                    }
                    this.$store.dispatch("loading", false)
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false)
                    if (err.response.status === 401) {
                        this.$store.dispatch("logout")
                        this.$router.push({
                            name: 'login'
                        });
                    }
                    if (err.response === undefined) {
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.error_message = err.response.data.message
                    }
                })
        }
    }
}
</script>
<style scoped>
.mt-15 {
	margin-top: 15px;
}
.mt-15 {
	margin-top: 15px;
}
</style>
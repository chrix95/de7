<template>
    <div class="row mtb-15">
        <div class="col-md-7">
            <h4>Product List</h4>
            <div class="alert alert-warning" v-if="has_error">
                {{ error_message }}
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Description</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody v-if="!loading && inventory.length !== 0">
                        <tr v-for="(item, index) in inventory" :key="item.id">
                            <th scope="row">{{ index + 1 }}</th>
                            <td>{{ item.name }}</td>
                            <td>{{ getCategoryName(item.categories_id) }}</td>
                            <td>{{ item.price }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.description.substr(0, 20) + '...' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" @click.prevent="editInventory(item.id)">Edit</button>
                                <button class="btn btn-danger btn-sm" @click.prevent="deleteInventory(item.id, item.name)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <th colspan="7">{{ loading ? 'Loading...' : 'No inventory found' }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-5">
            <h4>{{ create ? 'Create product' : 'Edit product' }}</h4>
            <div class="alert alert-warning" v-if="has_error_create">
                {{ error_message }}
            </div>
            <form @submit.prevent="create ? createInventory() : updateInventory()" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" v-model="payload.name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <select name="category" v-model="payload.categories_id" class="form-control">
                                <option value="" selected disabled>Select an option</option>
                                <option v-for="(cat, index) in categories" :key="index" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" name="price" min="1" class="form-control" v-model="payload.price">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="price" min="1" class="form-control" v-model="payload.quantity">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" v-model="payload.description" class="form-control" cols="30" rows="5" maxlength="255"></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary" :disabled="loading" v-if="create">{{ !loading ? 'Submit' : 'Please wait...' }}</button>
                            <button class="btn btn-sm btn-primary" :disabled="loading" v-else>{{ !loading ? 'Update' : 'Please wait...' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import InventoryService from "../services/InventoryService"
import CategoryService from "../services/CategoryService"
import { mapState } from 'vuex';
export default {
    name: 'ProductBlock',
    computed: {
        ...mapState(['loading']) 
    },
    mounted() {
        this.getCategory()
        this.getInventory()
    },
    data() {
        return {
            inventory: [],
            categories: [],
            payload: {
                categories_id: ''
            },
            has_error: false,
            create: true,
            has_error_create: false,
            error_message: ''
        }
    },
    methods: {
        getCategoryName(id) {
            return this.categories.find(c => c.id === id).name
        },
        getCategory() {
            this.$store.dispatch("loading", true)
            CategoryService.getCategories()
                .then((result) => {
                    if (result.data.status) {
                        this.categories = result.data.data
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
                        this.has_error_create = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.has_error_create = true
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
                        this.has_error_create = false
                    } else {
                        this.has_error_create = true
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
                        this.has_error_create = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.has_error_create = true
                        this.error_message = err.response.data.message
                    }
                })
        },
        updateInventory() {
            if (this.validateCreate()) { // use the same create validation for the 
                this.$store.dispatch("loading", true) // set the loading state
                InventoryService.updateInventory(this.payload)
                    .then((result) => {
                        if (result.data.status) {
                            this.create = true
                            this.inventory = this.inventory.filter(c => c.id !== this.payload.id)
                            this.inventory.push(result.data.data)
                            this.has_error_create = false
                            this.payload = {}
                            this.payload.categories_id = ''
                        } else {
                            this.has_error_create = true
                            this.error_message = result.data.message
                        }
                        this.$store.dispatch("loading", false)
                    })
                    .catch((err) => {
                        this.$store.dispatch("loading", false)
                        if (err.response.status === 401) {
                            // if token has expired, logout the user
                            this.$store.dispatch("logout")
                            this.$router.push({
                                name: 'login'
                            });
                        }
                        if (err.response === undefined) {
                            this.has_error_create = true
                            this.error_message = "Oops! took long to get a response"
                        } else {
                            this.has_error_create = true
                            this.error_message = err.response.data.message
                        }
                    })
            }
        },
        createInventory() {
            if (this.validateCreate()) {
                this.$store.dispatch("loading", true)
                InventoryService.createInventory(this.payload)
                    .then((result) => {
                        if (result.data.status) {
                            this.inventory = [...this.inventory, result.data.data]
                            this.has_error_create = false
                            this.payload = {}
                        } else {
                            this.has_error_create = true
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
                            this.has_error_create = true
                            this.error_message = "Oops! took long to get a response"
                        } else {
                            this.has_error_create = true
                            this.error_message = err.response.data.message
                        }
                    })
            }
        },
        validateCreate() {
            if(this.payload.name) {
                if(this.payload.categories_id) {
                    if(this.payload.price) {
                        if(this.payload.quantity) {
                            if(this.payload.description) {
                                return true
                            } else {
                                this.has_error_create = true,
                                this.error_message = 'Provide a product description'
                            }
                        } else {
                            this.has_error_create = true,
                            this.error_message = 'Provide a product quantity'
                        }
                    } else {
                        this.has_error_create = true,
                        this.error_message = 'Provide a product price'
                    }
                } else {
                    this.has_error_create = true,
                    this.error_message = 'Select a category'
                }
            } else {
                this.has_error_create = true,
                this.error_message = 'Product name is required'
            }
        },
        deleteInventory(id, name) {
            if (confirm(`This action cannot be undone as all products associated with ${name} will be deleted. Are you sure?`)) {
                InventoryService.deleteInventory({ id: id })
                    .then((result) => {
                        if (result.data.status) {
                            this.inventory = this.inventory.filter(cat => cat.id !== id)
                            this.has_error = false
                            this.create = true
                        } else {
                            this.has_error = true
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
                            this.has_error = true
                            this.error_message = "Oops! took long to get a response"
                        } else {
                            this.has_error = true
                            this.error_message = err.response.data.message
                        }
                    })
            }
        },
        editInventory(id) {
            var editList = this.inventory.find(cat => cat.id == id)
            this.payload = editList
            this.create = false
        }
    }
};
</script>
<style scoped>
.mtb-15 {
    margin: 15px auto;
}
</style>
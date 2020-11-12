<template>
    <div class="row mtb-15">
        <div class="col-md-6">
            <h4>Category List</h4>
            <div class="alert alert-warning" v-if="has_error">
                {{ error_message }}
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody v-if="!loading && categories.length !== 0">
                    <tr v-for="(cat, index) in categories" :key="cat.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>{{ cat.name }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" @click.prevent="editCategory(cat.id)">Edit</button>
                            <button class="btn btn-danger btn-sm" @click.prevent="deleteCategory(cat.id, cat.name)">Delete</button>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <th colspan="2">{{ loading ? 'Loading...' : 'No category found' }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4>{{ create ? 'Create category' : 'Edit category' }}</h4>
            <div class="alert alert-warning" v-if="has_error_create">
                {{ error_message }}
            </div>
            <form @submit.prevent="create ? createCategory() : updateCategory()" method="post">
                <div class="form-group">
                    <label for="name">Category name</label>
                    <input type="text" name="name" class="form-control" v-model="payload.name">
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" :disabled="loading" v-if="create">{{ !loading ? 'Submit' : 'Please wait...' }}</button>
                    <button class="btn btn-sm btn-primary" :disabled="loading" v-else>{{ !loading ? 'Update' : 'Please wait...' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import CategoryService from "../services/CategoryService"
import { mapState } from 'vuex';
export default {
    name: 'CategoryBlock',
    computed: {
        ...mapState(['loading'])
    },
    mounted() {
        this.getCategories()
    },
    data() {
        return {
            categories: [],
            payload: {},
            has_error: false,
            create: true,
            has_error_create: false,
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
        updateCategory() {
            if (this.validateCreate()) { // use the same create validation for the 
                this.$store.dispatch("loading", true) // set the loading state
                CategoryService.updateCategory(this.payload)
                    .then((result) => {
                        if (result.data.status) {
                            this.create = true
                            this.categories = this.categories.filter(c => c.id !== this.payload.id)
                            this.categories.push(result.data.data)
                            this.has_error_create = false
                            this.payload.name = ''
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
        createCategory() {
            if (this.validateCreate()) {
                this.$store.dispatch("loading", true)
                CategoryService.createCategory(this.payload)
                    .then((result) => {
                        if (result.data.status) {
                            this.categories = [...this.categories, result.data.data]
                            this.has_error_create = false
                            this.payload.name = ''
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
                return true
            } else {
                this.has_error_create = true,
                this.error_message = 'Category name is required'
            }
        },
        deleteCategory(id, name) {
            if (confirm(`This action cannot be undone as all products associated with ${name} will be deleted. Are you sure?`)) {
                CategoryService.deleteCategory({ id: id })
                    .then((result) => {
                        if (result.data.status) {
                            this.categories = this.categories.filter(cat => cat.id !== id)
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
        editCategory(id) {
            var editList = this.categories.find(cat => cat.id == id)
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
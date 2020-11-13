<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Category Grid</div>       
            <div class="card-body">
				<div class="row">
                    Category ID: {{ catId }}
				</div>
            </div>
        </div>
    </div>
</template>
<script>  
import { mapState } from 'vuex'
import CategoryService from "../../services/CategoryService"
export default {
	name: 'CategoryGriid',
	computed: {
		...mapState(['loading'])
    },
    props: ['catId'],
	mounted() {
		
	},
    data() {
      return {
        categories: []
      }
    },
    methods: {
      
    }
}
</script>
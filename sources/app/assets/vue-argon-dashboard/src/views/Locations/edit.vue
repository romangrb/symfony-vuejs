<template>
  <div class="card shadow">
    <div class="card-header bg-transparent">
      <h3 class="mb-0">Edit Location</h3>
    </div>
    <div class="card-body">
      <loading :active.sync="is_processing"
               :loader="loader"
               :can-cancel="true"
               :opacity="0.8"
               :is-full-page="is_full_page">
      </loading>

      <form role="form">
        <base-input class="mb-3"
                    placeholder="Name"
                    v-model="model.name"
                    label="Name"
                    v-bind:error="errors.name">
        </base-input>

        <div class="form-group mb-3">
          <label class="form-control-label">Description</label>
          <textarea class="form-control mb-3"
                    rows="3"
                    placeholder="Description"
                    label="Description"
                    v-model="model.description"></textarea>
        </div>

        <base-input class="mb-3"
                    placeholder="Latitude"
                    v-model="model.lat"
                    label="Latitude"
                    v-bind:error="errors.lat">
        </base-input>

        <base-input class="mb-3"
                    placeholder="Longitude"
                    v-model="model.lng"
                    label="Latitude"
                    v-bind:error="errors.lng">
        </base-input>

        <div class="d-flex justify-content-between">
          <base-button type="primary" @click="cancel" class="my-4">Cancel</base-button>
          <base-button type="success" @click="save" class="my-4">Save</base-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import http from '../../services/httpClient';
  import loading from 'vue-loading-overlay';
  import 'vue-loading-overlay/dist/vue-loading.css';

  export default {
    components: {
      loading
    },
    data() {
      return {
        is_processing: true,
        is_full_page: false,
        loader:'Dots',
        model: {
          name: '',
          description: '',
          lat: '',
          lng: ''
        },
        errors: {
          name: '',
          description: '',
          lat: '',
          lng: ''
        }
      }
    },
    mounted() {
      http.get('place/' + this.$route.params.id).then((data) => {
        this.model.name = data.name;
        this.model.description = data.description;
        this.is_processing = false;
      })
    },
    methods: {
      save: function() {
        this.is_processing = true;

        let _this = this;

        http.patch('place/' + this.$route.params.id, this.model).then((data) => {
          this.is_processing = false;
          this.$router.push({name: 'locations'});
        }).catch(function (err) {
          Object.keys(_this.errors).forEach(function(key) {
            _this.errors[key] = err.response.data[key] || '';
          });
          _this.is_processing = false;
        });
      },
      cancel: function() {
        this.$router.push({ name: 'locations' });
      }
    }
  };
</script>

<style></style>

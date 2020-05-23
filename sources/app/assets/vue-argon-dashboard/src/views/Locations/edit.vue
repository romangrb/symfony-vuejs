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

        <div>
          <gmap-map
                  :center="center"
                  :zoom="12"
                  style="width:100%;  height: 400px;"
                  @click="addMaker"
          >
          <gmap-marker
                  :key="index"
                  v-for="(m, index) in markers"
                  :position="m.position"
                  @click="center=m.position"
          ></gmap-marker>
          </gmap-map>
        </div>

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

  const MAX_MAKERS = 20;

  export default {
    name: "GoogleMap",
    components: {
      loading
    },
    data() {
      return {
        center: {lat: 0, lng: 0},
        markers: [],
        places: [],
        currentPlace: null,
        is_processing: true,
        is_full_page: false,
        loader:'Dots',
        model: {
          name: '',
          description: '',
          locations: []
        },
        errors: {
          name: '',
          description: '',
          locations: []
        }
      }
    },
    mounted() {
      http.get('place/' + this.$route.params.id).then((data) => {
        this.model.name = data.name;
        this.model.description = data.description;
        if (data['place_location'].length !== 0) {
          let first_el = data['place_location'][0];
          this.center.lat = first_el.lat;
          this.center.lng = first_el.lng;

        //  toDo add to locations to makers array to show on the map
        //  toDo save location array add validations
        //  toDo remove makers fn
        }
        this.is_processing = false;
      });
      this.geolocate();
    },
    methods: {
      addMaker(e) {

        if (this.markers.length > MAX_MAKERS ) {
          console.warn(`max maker limit ${MAX_MAKERS}`);
          return;
        }

        this.markers.push({
          id: 1 + Math.max(0, ...this.markers.map(n => n.id)),
          position: e.latLng,
        });
      },
      onMarkerClick(e) {
        this.$refs.map.panTo(e.latLng);
      },
      save: function() {
        this.model.locations = this.markers.map(item => {
          let position = item['position'];
            return {
              'lat': position.lat(),
              'lng': position.lng()
            };
        });

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

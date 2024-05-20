<template>
  <div>
    <nav class="navbar navbar-expand-lg custom-bg">
      <div class="container-fluid">
        <a class="navbar-brand text-white" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                User
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                <li>
                  <router-link to="/logout" class="dropdown-item">Logout</router-link>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <div class="container mt-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Create a Post</h5>
          <div class="mb-3">
            <textarea v-model="postContent" class="form-control" id="postContent" rows="3" placeholder="What's on your mind?"></textarea>
          </div>
          <div class="d-flex justify-content-end">
            <button @click="createPost" type="button" class="btn btn-primary">Post</button>
          </div>
        </div>
      </div>
    </div>
    <div class="container mt-4">
      <div v-for="(post, index) in posts" :key="index" class="card mb-3">
        <div class="card-body">
          <div class="avatar-container me-3">
                            <div class="avatar"></div>
                        </div>
          <p>{{ post.content }}</p>
          <p class="text-muted">Posted by: {{ post.author }}</p>
          <div class="d-flex justify-content-end">
            <button @click="editPost(index)" type="button" class="btn btn-sm btn-transparent">
              <img src="@/assets/EditIcon.png" alt="Edit Icon">
            </button>
            <button @click="deletePost(index)" type="button" class="btn btn-sm btn-transparent">
              <img src="@/assets/DeleteIcon.png" alt="Delete Icon">
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'HomePage',
  data() {
    return {
      postContent: '',
      posts: [],
    };
  },
  methods: {
    async createPost() {
        try {
            const response = await axios.post(`${this.$root.$data.apiUrl}/posts`, {
                content: this.postContent
            }, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            this.posts.push(response.data); // Add new post to the array
            this.postContent = ''; // Clear input field after posting
        } catch (error) {
            console.error('Error creating post:', error);
        }
    },
    async fetchPosts() {
      try {
        const response = await axios.get(`${this.$root.$data.apiUrl}/posts`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`, // Ensure token is sent with request
          },
        });
        this.posts = response.data;
      } catch (error) {
        console.error('Error fetching posts:', error);
      }
    },
  },
  mounted() {
    this.fetchPosts();
  },
};
</script>

<style>
.custom-bg {
  background-color: #1d1927; /* Replace with your desired color */
}

.text-white {
  color: white !important;
}

.card {
  margin-top: 20px;
}

.btn-primary {
  background-color: #007bff;
  border-color: #007bff;
}
</style>



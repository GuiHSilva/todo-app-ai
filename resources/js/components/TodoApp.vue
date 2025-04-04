<template>
  <div class="min-h-screen bg-gray-50 py-6 sm:py-12">
    <div class="max-w-xl sm:max-w-2xl md:max-w-4xl mx-auto px-4 sm:px-6">
      <h1 class="text-2xl sm:text-3xl font-bold mb-6 text-center sm:text-left text-gray-800">Lista de Tarefas</h1>

      <!-- Formulário para adicionar -->
      <div class="mb-8 bg-white p-4 sm:p-6 rounded-lg shadow-md">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">Adicionar Nova Tarefa</h2>
        <form @submit.prevent="addTodo" class="flex flex-col space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input
              type="text"
              id="title"
              v-model="newTodo.title"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              required
            >
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea
              id="description"
              v-model="newTodo.description"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="3"
            ></textarea>
          </div>

          <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-3 sm:space-y-0">
            <button
              type="submit"
              class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors w-full sm:w-auto"
            >
              Adicionar Tarefa
            </button>

            <button
              type="button"
              @click="openModal"
              class="bg-purple-600 text-white py-2 px-4 rounded-md hover:bg-purple-700 transition-colors w-full sm:w-auto"
            >
              Sugestão IA
            </button>
          </div>
        </form>
      </div>

      <!-- Lista de tarefas -->
      <div class="bg-white p-4 sm:p-6 rounded-lg shadow-md">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">Minhas Tarefas</h2>

        <div v-if="loading" class="text-center py-4">
          <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-600 mx-auto mb-2"></div>
          <p>Carregando...</p>
        </div>

        <div v-else-if="todos.length === 0" class="text-center py-8">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500">Nenhuma tarefa encontrada. Adicione sua primeira tarefa!</p>
        </div>

        <ul v-else class="divide-y divide-gray-200">
          <li v-for="todo in todos" :key="todo.id" class="py-4">
            <div class="flex flex-col sm:flex-row sm:items-start">
              <div class="flex items-start flex-1">
                <input
                  type="checkbox"
                  :checked="todo.completed"
                  @change="toggleTodoStatus(todo)"
                  class="mt-1 mr-3 h-4 w-4"
                >
                <div class="flex-1">
                  <h3 class="text-base sm:text-lg font-medium" :class="{ 'line-through text-gray-500': todo.completed }">
                    {{ todo.title }}
                  </h3>
                  <p class="text-gray-600 mt-1 text-sm sm:text-base" v-if="todo.description">{{ todo.description }}</p>
                </div>
              </div>
              <button
                @click="deleteTodo(todo)"
                class="mt-2 sm:mt-0 text-red-600 hover:text-red-800 py-1 px-2 rounded-md hover:bg-red-50 transition-colors self-end sm:self-start"
              >
                Excluir
              </button>
            </div>
          </li>
        </ul>
      </div>

      <!-- Modal de Sugestão IA -->
      <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-h-[90vh] w-full max-w-xs sm:max-w-lg md:max-w-2xl flex flex-col">
          <!-- Cabeçalho do Modal -->
          <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h3 class="text-lg sm:text-xl font-semibold text-gray-800">Sugestão de Tarefas</h3>
              <button
                @click="closeModal"
                class="text-gray-400 hover:text-gray-600 focus:outline-none"
              >
                <svg class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Conteúdo do Modal com scroll -->
          <div class="p-4 sm:p-6 overflow-y-auto flex-1">
            <!-- Estado de carregamento -->
            <div v-if="aiLoading" class="py-8 text-center">
              <div class="animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-t-2 border-b-2 border-purple-600 mx-auto mb-4"></div>
              <p>Buscando sugestões...</p>
            </div>

            <!-- Lista de sugestões -->
            <div v-else-if="aiSuggestions.length > 0" class="space-y-4">
              <p class="mb-3 text-gray-600">Aqui estão algumas sugestões de tarefas:</p>
              <ul class="space-y-4">
                <li v-for="(suggestion, index) in aiSuggestions" :key="index" class="p-3 sm:p-4 bg-purple-50 rounded-lg border border-purple-100 hover:bg-purple-100 transition-colors">
                  <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start">
                    <div class="mb-2 sm:mb-0 sm:mr-4">
                      <h4 class="font-medium text-base sm:text-lg text-purple-800">{{ suggestion.title }}</h4>
                      <p class="mt-1 text-sm sm:text-base text-gray-700">{{ suggestion.description }}</p>
                    </div>
                    <button
                      @click="addSuggestionToTodos(suggestion, index)"
                      class="self-end sm:self-start bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700 transition-colors flex-shrink-0"
                    >
                      Adicionar
                    </button>
                  </div>
                </li>
              </ul>
            </div>

            <!-- Formulário para obter sugestões -->
            <form v-else @submit.prevent="getAiSuggestions" class="space-y-4">
              <div>
                <label for="aiPrompt" class="block text-sm font-medium text-gray-700 mb-1">O que você pretende fazer?</label>
                <input
                  type="text"
                  id="aiPrompt"
                  v-model="aiPrompt"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                  placeholder="Ex: Organizar uma festa de aniversário"
                  required
                >
              </div>

              <div class="text-sm text-gray-500">
                Descreva o que você pretende fazer e a IA irá sugerir tarefas específicas para ajudar você a organizar.
              </div>
            </form>
          </div>

          <!-- Rodapé do Modal -->
          <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-3 space-y-3 space-y-reverse sm:space-y-0">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors"
              >
                Fechar
              </button>

              <button
                v-if="!aiSuggestions.length && !aiLoading"
                @click="getAiSuggestions"
                type="button"
                class="mb-3 sm:mb-0 px-4 py-2 text-white bg-purple-600 rounded-md hover:bg-purple-700 transition-colors"
                :disabled="!aiPrompt"
              >
                Obter Sugestões
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// Imports
import axios from 'axios';

export default {
  data() {
    return {
      todos: [],
      loading: true,
      newTodo: {
        title: '',
        description: '',
        completed: false
      },
      showModal: false,
      aiPrompt: '',
      aiLoading: false,
      aiSuggestions: []
    };
  },

  mounted() {
    this.fetchTodos();
  },

  methods: {
    fetchTodos() {
      this.loading = true;
      axios.get('/api/todos')
        .then(response => {
          this.todos = response.data;
        })
        .catch(error => {
          console.error('Erro ao buscar tarefas:', error);
          alert('Não foi possível carregar as tarefas. Por favor, tente novamente.');
        })
        .finally(() => {
          this.loading = false;
        });
    },

    addTodo() {
      axios.post('/api/todos', this.newTodo)
        .then(response => {
          this.todos.push(response.data);
          this.newTodo.title = '';
          this.newTodo.description = '';
        })
        .catch(error => {
          console.error('Erro ao adicionar tarefa:', error);
          alert('Não foi possível adicionar a tarefa. Por favor, tente novamente.');
        });
    },

    toggleTodoStatus(todo) {
      const updatedTodo = {
        ...todo,
        completed: !todo.completed
      };

      axios.put(`/api/todos/${todo.id}`, updatedTodo)
        .then(response => {
          const index = this.todos.findIndex(t => t.id === todo.id);
          this.todos[index] = response.data;
        })
        .catch(error => {
          console.error('Erro ao atualizar tarefa:', error);
          alert('Não foi possível atualizar a tarefa. Por favor, tente novamente.');
        });
    },

    deleteTodo(todo) {
      if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
        axios.delete(`/api/todos/${todo.id}`)
          .then(() => {
            this.todos = this.todos.filter(t => t.id !== todo.id);
          })
          .catch(error => {
            console.error('Erro ao excluir tarefa:', error);
            alert('Não foi possível excluir a tarefa. Por favor, tente novamente.');
          });
      }
    },

    openModal() {
      this.showModal = true;
      this.aiPrompt = '';
      this.aiSuggestions = [];
    },

    closeModal() {
      this.showModal = false;
    },

    getAiSuggestions() {
      if (!this.aiPrompt) return;

      this.aiLoading = true;

      axios.post('/api/ai-suggest', { prompt: this.aiPrompt })
        .then(response => {
          this.aiSuggestions = response.data.suggestions || [];
        })
        .catch(error => {
          console.error('Erro ao obter sugestões:', error);
          alert('Não foi possível obter sugestões. Por favor, tente novamente.');
        })
        .finally(() => {
          this.aiLoading = false;
        });
    },

    addSuggestionToTodos(suggestion, index) {
      const newTodo = {
        title: suggestion.title,
        description: suggestion.description,
        completed: false
      };

      axios.post('/api/todos', newTodo)
        .then(response => {
          // Adiciona a nova tarefa à lista de todos
          this.todos.push(response.data);

          // Remove a sugestão da lista de sugestões
          this.aiSuggestions.splice(index, 1);

          // Fecha o modal automaticamente se não houver mais sugestões
          if (this.aiSuggestions.length === 0) {
            this.closeModal();
          }
        })
        .catch(error => {
          console.error('Erro ao adicionar sugestão como tarefa:', error);
          alert('Não foi possível adicionar a tarefa. Por favor, tente novamente.');
        });
    }
  }
};
</script>
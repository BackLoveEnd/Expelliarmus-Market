<script setup>
import {onMounted} from "vue";
import {useRouter} from "vue-router";

const router = useRouter();

onMounted(() => {
  if (!history.state.redirected) {
    router.push('/');
  }

  if (router.currentRoute.value.fullPath === "/500") {
    let redirectCount = parseInt(sessionStorage.getItem("redirectCount")) || 0;

    if (redirectCount >= 2) {
      sessionStorage.removeItem("redirectCount");
      return;
    }

    sessionStorage.setItem("redirectCount", redirectCount + 1);

    setTimeout(() => {
      router.push("/");
    }, 4000);
  } else {
    sessionStorage.removeItem("redirectCount");
  }
});
</script>

<template>
  <main class="grid place-items-center h-[100vh]">
    <section class="bg-white dark:bg-gray-900">
      <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center">
          <h1 class="mb-4 text-7xl tracking-tight font-extrabold lg:text-9xl text-red-500 dark:text-primary-500">
            500</h1>
          <p class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white">Internal Server
            Error.</p>
          <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">We are already working to solve the
            problem. </p>
          <p class="mb-4 text-base font-light text-gray-500 dark:text-gray-400">You will be redirected soon.</p>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>

</style>
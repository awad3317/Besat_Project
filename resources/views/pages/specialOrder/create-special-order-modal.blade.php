<div x-data="{ isModalOpen: false}">
  <button @click="isModalOpen = true"
    class="bg-brand-500 hover:bg-brand-600 h-10 rounded-lg px-6 py-2 text-sm font-medium text-white min-w-[100px]">
    إنشاء رحلة جديدة
  </button>
  <div x-show="isModalOpen" class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto modal z-99999"
    style="display: none;">

    <div class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]">
    </div>

    <div @click.outside="isModalOpen = false"
      class="relative w-full max-w-[630px] rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">
      <form method="POST" action="{{ route('specialOrder.store') }}" enctype="multipart/form-data">
        @csrf
        <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">
          إنشاء رحلة جديدة
        </h4>
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

          <div class="col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              عنوان الرحلة <span class="text-brand-600 dark:text-brand-400">*</span>
            </label>
            <input type="text" placeholder="مثال: رحلة خاصة" name="title"
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white"
              required>
          </div>

          <div class="col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              أسم العميل <span class="text-brand-600 dark:text-brand-400">*</span></label>
            <input type="text" placeholder="مثال: أحمد شرجبي" name="customer_name" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
          </div>

          <div class="col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              رقم جوال العميل <span class="text-brand-600 dark:text-brand-400">*</span></label>
            <input type="text" placeholder="مثال:967780236552" name="customer_phone" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
          </div>

          <div class="col-span-1 sm:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              رقم الواتساب للعميل
            </label>
            <input type="text" placeholder="مثال:967780236552" name="customer_whatsapp"
              class="hover:border-brand-500 dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs resize-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
          </div>

          <div class="col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              من   <span class="text-brand-600 dark:text-brand-400">*</span></label>
            <input type="text" placeholder="مثال: المنصورة - سوق القات" name="customer_phone" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
          </div>

          <div class="col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              الى<span class="text-brand-600 dark:text-brand-400">*</span></label>
            <input type="text" placeholder="مثال: المعلا - اسكريم المعلا" name="customer_phone" required
              class="hover:border-brand-500 dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-500 focus:ring-1 focus:ring-brand-500 dark:border-gray-600 dark:text-white">
          </div>
        </div>

        <div class="flex items-center justify-end w-full gap-3 mt-6">
          <button @click="isModalOpen = false" type="button"
            class="hover:border-brand-500 flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 sm:w-auto">
            إغلاق
          </button>
          <button type="submit"
            class="flex justify-center hover:bg-brand-600 w-full px-4 py-3 text-sm font-medium text-white rounded-lg bg-brand-500">
            إنشاء رحلة
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
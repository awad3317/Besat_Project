<aside :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : 'translate-x-full'"
  class="sidebar fixed right-0 top-0 z-40 flex h-screen w-[290px] flex-col overflow-y-hidden border-l border-gray-200 bg-white px-5 dark:border-gray-800 dark:bg-black duration-300 ease-in-out transition-transform lg:static lg:translate-x-0"
  @click.outside="sidebarToggle = false">


  <!-- SIDEBAR HEADER -->
<div class="flex justify-center items-center px-4 pt-8 pb-4 transition-all duration-300 sidebar-header">
    <a href="#" class="flex justify-center items-center w-full">
        
        <!-- الشعار الكامل (يظهر عندما تكون القائمة الجانبية مفتوحة) -->
        <span class="flex justify-center w-full transition-all duration-300 logo" :class="sidebarToggle ? 'hidden' : 'block'">
            <!-- الشعار الفاتح -->
            <img class="object-contain mx-auto w-auto max-w-full h-14 dark:hidden" 
                 src="{{ asset('tailadmin/build/src/images/user/besat_light.png') }}"
                 alt="Besat Logo Light" />
                 
            <!-- الشعار الداكن -->
            <img class="hidden object-contain mx-auto w-auto max-w-full h-14 dark:block" 
                 src="{{ asset('tailadmin/build/src/images/user/besat_dark.png') }}"
                 alt="Besat Logo Dark" />
        </span>

        <!-- الأيقونة المصغرة (تظهر عندما تكون القائمة الجانبية مغلقة) -->
    <img class="object-contain mx-auto w-12 h-12 transition-all duration-300 scale-125 shrink-0 logo-icon" 
     :class="sidebarToggle ? 'block' : 'hidden'"
     src="{{ asset('tailadmin/build/src/images/user/Busat.png') }}" 
     alt="Besat Icon" />
    </a>
</div>
  <!-- SIDEBAR HEADER -->

  <div class="flex overflow-y-auto flex-col duration-300 ease-linear no-scrollbar">
    <!-- Sidebar Menu -->
    <nav x-data="{ selected: $persist('Dashboard') }">
      <!-- Menu Group -->
      <div>
        {{-- <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            القائمة الرئيسية
          </span>

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="" />
          </svg>
        </h3> --}}
        <ul class="flex flex-col gap-4 mb-6">
          <!-- Menu Item Dashboard -->
          <li>
            <a href="{{ route('dashboard.index') }}" @click="selected = 'Dashboard'"
              class="menu-item group" :class="window.location.href.includes('{{ route('dashboard.index') }}') ?
                                'menu-item-active' : 'menu-item-inactive'">
              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
                <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
                <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
                <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                الصفحة الرئيسية
              </span>
            </a>
          </li>

          <!-- Menu Item users -->
          <li>
            <a href="{{ route('users.index') }}" @click="selected = (selected === 'users' ? '':'users')"
              class="menu-item group" :class="window.location.href.includes('{{ route('users.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">
              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                ادارة المستخدمين
              </span>
            </a>
          </li>

          <!-- Menu Item Chat -->
          @php
            $unreadChatMessagesCount = (int) \App\Models\Conversation::sum('participant_unread_count');
          @endphp
          <li x-data="{
                unreadCount: {{ $unreadChatMessagesCount }},
                init() {
                    window.addEventListener('show-firebase-notification', (event) => {
                        if (event.detail && (event.detail.type === 'support_chat' || event.detail.new_message === 'true')) {
                            this.unreadCount++;
                        }
                    });
                    window.addEventListener('chat-messages-read', (event) => {
                        const readCount = (event.detail && event.detail.count) ? parseInt(event.detail.count) : 0;
                        this.unreadCount = Math.max(0, this.unreadCount - readCount);
                    });
                }
              }">
            <a href="{{ route('chats.index') }}" @click="selected = (selected === 'Chat' ? '':'Chat')"
              class="menu-item group relative" :class="window.location.href.includes('{{ route('chats.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <div class="relative inline-flex items-center justify-center w-6 h-6 shrink-0">
                <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 13.8214 2.48697 15.5291 3.33782 17L2.5 21.5L7 20.6622C8.47087 21.513 10.1786 22 12 22Z"
                    stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M8 12H8.01M12 12H12.01M16 12H16.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
                <span x-show="unreadCount > 0 && sidebarToggle" x-cloak
                  style="position: absolute; top: -2px; right: -2px; width: 9px; height: 9px; border-radius: 9999px; background-color: #dc6803; border: 1.5px solid #fff;"></span>
              </div>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''"
                style="display: flex; align-items: center; justify-content: space-between; flex: 1;">
                <span>المحادثات</span>
                <template x-if="unreadCount > 0">
                  <span
                    style="display: inline-flex; align-items: center; justify-content: center; text-align: center; min-width: 22px; height: 22px; padding: 0 5px; border-radius: 5px; background-color: #dc6803; color: #ffffff; font-size: 12px; font-weight: 700; line-height: 22px;"
                    x-text="unreadCount > 99 ? '+99' : unreadCount">
                  </span>
                </template>
              </span>
            </a>
          </li>

          <!-- Menu Item drivers -->
          <li>
            <a href="{{ route('drivers.index') }}" @click="selected = (selected === 'Drivers' ? '':'Drivers')"
              class="menu-item group" :class="window.location.href.includes('{{ route('drivers.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">
              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" />
                <circle cx="12" cy="12" r="2.25" />
                <path d="M12 14.25V21" />
                <path d="M9.83 11.42L3.31 9.67" />
                <path d="M14.17 11.42L20.69 9.67" />
              </svg>
              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة السائقين
              </span>
            </a>
          </li>

          <!-- Menu Item Requests -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'Requests' ? '' : 'Requests')"
              class="flex relative items-center menu-item group" :class="(selected === 'Requests') || window.location.href.includes(
                                '{{ route('request.index') }}') || window.location.href.includes(
                                '{{ route('specialOrder.index') }}') ? 'menu-item-active' : 'menu-item-inactive'">

              <!-- السهم المطلق في أقصى اليسار -->
              <svg class="absolute left-4 transition-transform duration-300"
                :class="[(selected === 'Requests') ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']" width="20" height="20" viewBox="0 0 20 20"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>

              <!-- الأيقونة -->
              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2" />
                <rect x="9" y="3" width="6" height="4" rx="1" />
                <path d="M9 12h6" />
                <path d="M9 16h4" />
              </svg>

              <!-- النص -->
              <span class="flex-1 menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة الطلبات
              </span>
            </a>
            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'Requests') ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
                <li>
                  <a href="{{ route('request.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('request.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    طلبات عامة
                  </a>
                </li>
                <li>
                  <a href="{{ route('specialOrder.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('specialOrder.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    طلبات خاصة
                  </a>
                </li>

              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>

          <!-- Menu Item Customer Ratings -->
          <li>
            <a href="{{ route('ratings.index') }}" @click="selected = (selected === 'Ratings' ? '':'Ratings')"
              class="menu-item group" :class="window.location.href.includes('{{ route('ratings.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"
                  stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                تقييمات العملاء
              </span>
            </a>
          </li>

          <!-- Menu Item Coupon -->
          <li>
            <a href="{{ route('Coupon.index') }}" @click="selected = (selected === 'Coupons' ? '':'Coupons')"
              class="menu-item group" :class="window.location.href.includes('{{ route('Coupon.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                كوبونات الخصم
              </span>
            </a>
          </li>

          @if (Auth::user()?->type === 'superAdmin')
            <li>
              <a href="{{ route('Bank.index') }}" @click="selected = (selected === 'Banks' ? '':'Banks')"
                class="menu-item group" :class="window.location.href.includes('{{ route('Bank.index') }}') ? 'menu-item-active' :
              'menu-item-inactive'">

                <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                  xmlns="http://www.w3.org/2000/svg">
                  <path d="M3 21h18" />
                  <path d="M3 10h18" />
                  <path d="M5 6l7-3 7 3" />
                  <path d="M4 10v11" />
                  <path d="M20 10v11" />
                  <path d="M8 14v3" />
                  <path d="M12 14v3" />
                  <path d="M16 14v3" />
                </svg>

                <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                  إدارة البنوك
                </span>
              </a>
            </li>
          @endif

          <!-- Menu Item Vehicle -->
          <li>
            <a href="{{ route('Vehicle.index') }}" @click="selected = (selected === 'Vehicles' ? '':'Vehicles')"
              class="menu-item group" :class="window.location.href.includes('{{ route('Vehicle.index') }}') ? 'menu-item-active' :
                                'menu-item-inactive'">

              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2" />
                <circle cx="7" cy="17" r="2" />
                <path d="M9 17h6" />
                <circle cx="17" cy="17" r="2" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                إدارة المركبات
              </span>
            </a>
          </li>

          <!-- Menu Item setting -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'setting' ? '' : 'setting')"
              class="flex relative items-center menu-item group" :class="(selected === 'setting') 
                            || window.location.href.includes('{{ route('systems.index') }}')
                            || window.location.href.includes('{{ route('notifications.index') }}')
                            || window.location.href.includes('{{ route('admins.index') }}')
                            || window.location.href.includes('{{ route('log.index') }}')
                            || window.location.href.includes('{{ route('ads.index') }}')
                             ? 'menu-item-active' : 'menu-item-inactive'">

              <!-- السهم المطلق في أقصى اليسار -->
              <svg class="absolute left-4 transition-transform duration-300"
                :class="[(selected === 'setting') ? 'rotate-180' : '', sidebarToggle ? 'lg:hidden' : '']" width="20" height="20" viewBox="0 0 20 20"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="currentColor" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>

              <!-- الأيقونة -->
              <svg class="w-6 h-6 shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"
                xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <circle cx="12" cy="12" r="3" />
              </svg>

              <!-- النص -->
              <span class="flex-1 menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                الأعدادات
              </span>
            </a>
            <div class="overflow-hidden transform translate" :class="(selected === 'setting') ? 'block' : 'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
                <li>
                  <a href="{{ route('systems.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('systems.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    النظام
                  </a>
                </li>
                @if (Auth::user()?->type === 'superAdmin')
                  <li>
                    <a href="{{ route('admins.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('admins.index') }}') ?
                                              'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                      المستخدمين
                    </a>
                  </li>
                @endif
                <li>
                  <a href="{{ route('notifications.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('notifications.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    الاشعارات
                  </a>
                </li>
                <li>
                  <a href="{{ route('log.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('log.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    سجل النشاطات
                  </a>
                </li>
                <li>
                  <a href="{{ route('ads.index') }}" class="menu-dropdown-item group" :class="window.location.href.includes('{{ route('ads.index') }}') ?
                                            'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    الإعلانات
                  </a>
                </li>
              </ul>
            </div>
          </li>


          <!-- Menu Item Pages -->
        </ul>
      </div>

      <!-- Others Group -->
      {{-- <div>
        <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400">
          <span class="menu-group-title" :class="sidebarToggle ? 'lg:hidden' : ''">
            others
          </span>

          <svg :class="sidebarToggle ? 'lg:block hidden' : 'hidden'" class="mx-auto fill-current menu-group-icon"
            width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z"
              fill="" />
          </svg>
        </h3>

        <ul class="flex flex-col gap-4 mb-6">
          <!-- Menu Item Charts -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'Charts' ? '':'Charts')" class="menu-item group"
              :class="(selected === 'Charts') || (page === 'lineChart' || page === 'barChart' || page === 'pieChart') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'Charts') || (page === 'lineChart' || page === 'barChart' || page === 'pieChart') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M12 2C11.5858 2 11.25 2.33579 11.25 2.75V12C11.25 12.4142 11.5858 12.75 12 12.75H21.25C21.6642 12.75 22 12.4142 22 12C22 6.47715 17.5228 2 12 2ZM12.75 11.25V3.53263C13.2645 3.57761 13.7659 3.66843 14.25 3.80098V3.80099C15.6929 4.19606 16.9827 4.96184 18.0104 5.98959C19.0382 7.01734 19.8039 8.30707 20.199 9.75C20.3316 10.2341 20.4224 10.7355 20.4674 11.25H12.75ZM2 12C2 7.25083 5.31065 3.27489 9.75 2.25415V3.80099C6.14748 4.78734 3.5 8.0845 3.5 12C3.5 16.6944 7.30558 20.5 12 20.5C15.9155 20.5 19.2127 17.8525 20.199 14.25H21.7459C20.7251 18.6894 16.7492 22 12 22C6.47715 22 2 17.5229 2 12Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Charts
              </span>

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'Charts') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'Charts') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
                <li>
                  <a href="line-chart.html" class="menu-dropdown-item group"
                    :class="page === 'lineChart' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Line Chart
                  </a>
                </li>
                <li>
                  <a href="bar-chart.html" class="menu-dropdown-item group"
                    :class="page === 'barChart' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Bar Chart
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Charts -->

          <!-- Menu Item Ui Elements -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'UIElements' ? '':'UIElements')"
              class="menu-item group"
              :class="(selected === 'UIElements') || (page === 'alerts' || page === 'avatars' || page === 'badge' || page === 'buttons' || page === 'buttonsGroup' || page === 'cards'|| page === 'carousel' || page === 'dropdowns' || page === 'images' || page === 'list' || page === 'modals' || page === 'videos') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'UIElements') || (page === 'alerts' || page === 'avatars' || page === 'badge' || page === 'breadcrumb' || page === 'buttons' || page === 'buttonsGroup' || page === 'cards'|| page === 'carousel' || page === 'dropdowns' || page === 'images' || page === 'list' || page === 'modals' || page === 'notifications' || page === 'popovers' || page === 'progress' || page === 'spinners' || page === 'tooltips' || page === 'videos') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M11.665 3.75618C11.8762 3.65061 12.1247 3.65061 12.3358 3.75618L18.7807 6.97853L12.3358 10.2009C12.1247 10.3064 11.8762 10.3064 11.665 10.2009L5.22014 6.97853L11.665 3.75618ZM4.29297 8.19199V16.0946C4.29297 16.3787 4.45347 16.6384 4.70757 16.7654L11.25 20.0365V11.6512C11.1631 11.6205 11.0777 11.5843 10.9942 11.5425L4.29297 8.19199ZM12.75 20.037L19.2933 16.7654C19.5474 16.6384 19.7079 16.3787 19.7079 16.0946V8.19199L13.0066 11.5425C12.9229 11.5844 12.8372 11.6207 12.75 11.6515V20.037ZM13.0066 2.41453C12.3732 2.09783 11.6277 2.09783 10.9942 2.41453L4.03676 5.89316C3.27449 6.27429 2.79297 7.05339 2.79297 7.90563V16.0946C2.79297 16.9468 3.27448 17.7259 4.03676 18.1071L10.9942 21.5857L11.3296 20.9149L10.9942 21.5857C11.6277 21.9024 12.3732 21.9024 13.0066 21.5857L19.9641 18.1071C20.7264 17.7259 21.2079 16.9468 21.2079 16.0946V7.90563C21.2079 7.05339 20.7264 6.27429 19.9641 5.89316L13.0066 2.41453Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                UI Elements
              </span>

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'UIElements') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate" :class="(selected === 'UIElements') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
                <li>
                  <a href="alerts.html" class="menu-dropdown-item group"
                    :class="page === 'alerts' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Alerts
                  </a>
                </li>
                <li>
                  <a href="avatars.html" class="menu-dropdown-item group"
                    :class="page === 'avatars' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Avatars
                  </a>
                </li>
                <li>
                  <a href="badge.html" class="menu-dropdown-item group"
                    :class="page === 'badge' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Badges
                  </a>
                </li>
                <li>
                  <a href="buttons.html" class="menu-dropdown-item group"
                    :class="page === 'buttons' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Buttons
                  </a>
                </li>
                <li>
                  <a href="images.html" class="menu-dropdown-item group"
                    :class="page === 'images' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Images
                  </a>
                </li>
                <li>
                  <a href="videos.html" class="menu-dropdown-item group"
                    :class="page === 'videos' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Videos
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Ui Elements -->

          <!-- Menu Item Authentication -->
          <li>
            <a href="#" @click.prevent="selected = (selected === 'Authentication' ? '':'Authentication')"
              class="menu-item group"
              :class="(selected === 'Authentication') || (page === 'basicChart' || page === 'advancedChart') ? 'menu-item-active' : 'menu-item-inactive'">
              <svg
                :class="(selected === 'Authentication') || (page === 'basicChart' || page === 'advancedChart') ? 'menu-item-icon-active'  :'menu-item-icon-inactive'"
                width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M14 2.75C14 2.33579 14.3358 2 14.75 2C15.1642 2 15.5 2.33579 15.5 2.75V5.73291L17.75 5.73291H19C19.4142 5.73291 19.75 6.0687 19.75 6.48291C19.75 6.89712 19.4142 7.23291 19 7.23291H18.5L18.5 12.2329C18.5 15.5691 15.9866 18.3183 12.75 18.6901V21.25C12.75 21.6642 12.4142 22 12 22C11.5858 22 11.25 21.6642 11.25 21.25V18.6901C8.01342 18.3183 5.5 15.5691 5.5 12.2329L5.5 7.23291H5C4.58579 7.23291 4.25 6.89712 4.25 6.48291C4.25 6.0687 4.58579 5.73291 5 5.73291L6.25 5.73291L8.5 5.73291L8.5 2.75C8.5 2.33579 8.83579 2 9.25 2C9.66421 2 10 2.33579 10 2.75L10 5.73291L14 5.73291V2.75ZM7 7.23291L7 12.2329C7 14.9943 9.23858 17.2329 12 17.2329C14.7614 17.2329 17 14.9943 17 12.2329L17 7.23291L7 7.23291Z"
                  fill="" />
              </svg>

              <span class="menu-item-text" :class="sidebarToggle ? 'lg:hidden' : ''">
                Authentication
              </span>

              <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 stroke-current menu-item-arrow"
                :class="[(selected === 'Authentication') ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive', sidebarToggle ? 'lg:hidden' : '' ]"
                width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.39584L10.0001 12.6042L15.2084 7.39585" stroke="" stroke-width="1.5"
                  stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </a>

            <!-- Dropdown Menu Start -->
            <div class="overflow-hidden transform translate"
              :class="(selected === 'Authentication') ? 'block' :'hidden'">
              <ul :class="sidebarToggle ? 'lg:hidden' : 'flex'" class="flex flex-col gap-1 pl-9 mt-2 menu-dropdown">
                <li>
                  <a href="signin.html" class="menu-dropdown-item group"
                    :class="page === 'signin' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Sign In
                  </a>
                </li>
                <li>
                  <a href="signup.html" class="menu-dropdown-item group"
                    :class="page === 'signup' ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'">
                    Sign Up
                  </a>
                </li>
              </ul>
            </div>
            <!-- Dropdown Menu End -->
          </li>
          <!-- Menu Item Authentication -->
        </ul>
      </div> --}}
    </nav>
    <!-- Sidebar Menu -->
  </div>
</aside>
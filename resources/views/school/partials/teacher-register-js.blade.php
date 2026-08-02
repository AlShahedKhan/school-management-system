  <script>
      const teacherModal = document.getElementById('teacherModal');
      const photoInput = document.getElementById('photoInput');
      const imagePreview = document.getElementById('imagePreview');
      const teacherMobileInput = document.querySelector('#teacherForm input[name="mobile"]');
      const teacherMobileError = document.getElementById('teacherMobileError');
      let currentPage = {{ isset($teachers) ? $teachers->currentPage() : 1 }};

      function sanitizeEnglishDigits(value) {
          return value.replace(/[^0-9]/g, '');
      }

      function setTeacherMobileError(show, message = 'Must provide numbers only.') {
          teacherMobileError.textContent = message;
          teacherMobileError.classList.toggle('hidden', !show);
          teacherMobileInput.classList.toggle('border-red-500', show);
          teacherMobileInput.classList.toggle('focus:border-red-500', show);
          teacherMobileInput.setCustomValidity(show ? message : '');
      }

      function formatDate(dateStr) {
          if (!dateStr) return '';
          const d = new Date(dateStr);
          if (isNaN(d.getTime())) return dateStr;
          const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
          return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
      }

      function togglePass(id, trigger) {
          const input = document.getElementById(id);
          const icon = trigger.querySelector('i');
          if (input.type === "password") {
              input.type = "text";
              icon.classList.replace('mdi-eye', 'mdi-eye-off');
              trigger.setAttribute('aria-label', 'Hide password');
          } else {
              input.type = "password";
              icon.classList.replace('mdi-eye-off', 'mdi-eye');
              trigger.setAttribute('aria-label', 'Show password');
          }
      }
      photoInput.addEventListener('change', function() {
          const file = this.files[0];
          if (file) {
              const reader = new FileReader();
              reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" />`;
              reader.readAsDataURL(file);
          } else {
              imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
          }
      });

      teacherMobileInput.addEventListener('input', function() {
          const sanitizedValue = sanitizeEnglishDigits(this.value);
          const hasInvalidChars = this.value !== sanitizedValue;

          this.value = sanitizedValue;
          setTeacherMobileError(hasInvalidChars);
      });

      teacherMobileInput.addEventListener('paste', function(event) {
          event.preventDefault();
          const pastedText = (event.clipboardData || window.clipboardData).getData('text');
          const sanitizedValue = sanitizeEnglishDigits(pastedText);

          this.value = sanitizedValue;
          setTeacherMobileError(pastedText !== sanitizedValue);
      });

      document.getElementById('openTeacherModal')?.addEventListener('click', () => {
          document.getElementById('teacherForm').reset();
          document.getElementById('teacher_id').value = '';
          imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
          setTeacherMobileError(false);
          teacherModal.classList.remove('hidden');
      });
      document.getElementById('closeTeacherModal')?.addEventListener('click', () => {
          teacherModal.classList.add('hidden');
          if (window.lastActiveModalId) {
              document.getElementById(window.lastActiveModalId)?.classList.remove('hidden');
              window.lastActiveModalId = null;
          }
      });
      const getTeacherSearchValue = () => {
          const desktopSearch = document.getElementById('teacherSearch');
          const mobileSearch = document.getElementById('teacherSearchMobile');
          const activeElement = document.activeElement;

          if (activeElement === mobileSearch) {
              return mobileSearch.value;
          }

          if (activeElement === desktopSearch) {
              return desktopSearch.value;
          }

          return desktopSearch?.value || mobileSearch?.value || '';
      };

      const reloadTeacherPage = (page = 1) => {
          const url = new URL('{{ route('school.teacher-registration') }}', window.location.origin);
          const search = getTeacherSearchValue().trim();
          const teacherId = document.getElementById('teacherFilter')?.value?.trim() || '';

          if (search) {
              url.searchParams.set('search', search);
          }

          if (teacherId) {
              url.searchParams.set('teacher_id', teacherId);
          }

          if (page > 1) {
              url.searchParams.set('page', page);
          }

          window.location.href = url.toString();
      };

      const fetchTeachers = (page = 1) => {
          currentPage = page;
          const tbody = document.getElementById('teacherTableBody');

          if (!tbody) {
              reloadTeacherPage(page);
              return;
          }

          const search = getTeacherSearchValue();
          const teacherId = document.getElementById('teacherFilter')?.value || '';
          axios.get('{{ url('/api/teachers') }}', {
                  params: {
                      search,
                      teacher_id: teacherId,
                      per_page: 30,
                      page
                  }
              })
              .then(res => {
                  const teachers = res.data.data || res.data;
                  const meta = res.data.meta || {
                      current_page: 1,
                      last_page: 1,
                      total: teachers.length,
                      from: 1,
                      to: teachers.length
                  };
                  tbody.innerHTML = '';
                  teachers.forEach((t, index) => {
                      const sl = ((meta.current_page - 1) * (meta.per_page || teachers.length)) + index + 1;
                      const photoUrl = t.photo ? `/storage/${t.photo}` :
                          'https://ui-avatars.com/api/?background=random&name=' + t.name;
                      const statusVal = t.status || 'Active';
                      const statusHtml = statusVal === 'Hold' 
                          ? `<span class="px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Hold</span>`
                          : `<span class="px-2 py-0.5 text-[10px] font-semibold bg-green-100 text-green-700 rounded-full">Active</span>`;

                      tbody.innerHTML += `
                     <tr class="teacher-row">
                         <td class="teacher-cell whitespace-nowrap text-center align-middle" data-label="SL">${sl}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Photo"><img src="${photoUrl}" alt="${t.name}" /></td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Name">${t.name}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="ID Number">${t.id_number || 'PENDING'}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Designation">${t.designation || 'N/A'}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Phone">${t.mobile}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Email">${t.email}</td>
                         <td class="teacher-cell whitespace-nowrap" data-label="Status">${statusHtml}</td>
                          <td class="teacher-cell whitespace-nowrap text-center align-middle" data-label="Action">
                              <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                  <button
                                      type="button"
                                      onclick="editTeacher(${t.id})"
                                      title="Edit ${t.name}"
                                      aria-label="Edit ${t.name}"
                                      class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1">
                                      <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                  </button>
                                  <button
                                      type="button"
                                      onclick="toggleTeacherStatus(${t.id}, '${statusVal}', '${t.name.replace(/'/g, "\\'")}', '${(t.designation || '').replace(/'/g, "\\'")}', '${t.id_number}')"
                                      title="Toggle Status for ${t.name}"
                                      aria-label="Toggle Status for ${t.name}"
                                      class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-slate-700 focus-visible:ring-slate-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1">
                                      <i class="fas fa-toggle-on text-xs" aria-hidden="true"></i>
                                  </button>
                                  <button
                                      type="button"
                                      onclick="deleteTeacher(${t.id})"
                                      title="Delete ${t.name}"
                                      aria-label="Delete ${t.name}"
                                      class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1">
                                      <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                  </button>
                              </div>
                          </td>
                     </tr>`;
                  });
                  renderPagination(meta);
              }).catch(e => console.error("Load failed", e));
      }

      function renderPagination(meta) {
          const controls = document.getElementById('paginationControls');
          const info = document.getElementById('paginationInfo');
          info.innerText = `${meta.to || 0} of ${meta.total}`;
          controls.innerHTML = '';
          const prevBtn = document.createElement('button');
          prevBtn.className = 'pagination-btn';
          prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
          prevBtn.disabled = meta.current_page === 1;
          prevBtn.onclick = () => fetchTeachers(meta.current_page - 1);
          controls.appendChild(prevBtn);
          for (let i = 1; i <= meta.last_page; i++) {
              if (i > 5 && i < meta.last_page) continue;
              const pgBtn = document.createElement('button');
              pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
              pgBtn.innerText = i;
              pgBtn.onclick = () => fetchTeachers(i);
              controls.appendChild(pgBtn);
          }
          const nextBtn = document.createElement('button');
          nextBtn.className = 'pagination-btn';
          nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
          nextBtn.disabled = meta.current_page === meta.last_page;
          nextBtn.onclick = () => fetchTeachers(meta.current_page + 1);
          controls.appendChild(nextBtn);
      }
      if (document.getElementById('teacherTableBody')) {
          fetchTeachers();
      }

      let teacherSearchTimer = null;
      const bindTeacherSearch = (input, peer) => {
          if (!input) {
              return;
          }

          input.addEventListener('input', () => {
              if (peer) {
                  peer.value = input.value;
              }

              clearTimeout(teacherSearchTimer);
              teacherSearchTimer = setTimeout(() => fetchTeachers(1), 450);
          });
      };

      bindTeacherSearch(document.getElementById('teacherSearch'), document.getElementById('teacherSearchMobile'));
      bindTeacherSearch(document.getElementById('teacherSearchMobile'), document.getElementById('teacherSearch'));
      document.getElementById('teacherForm').addEventListener('submit', function(e) {
          e.preventDefault();
          const tid = document.getElementById('teacher_id').value;
          const formData = new FormData(this);
          const sanitizedMobile = sanitizeEnglishDigits(formData.get('mobile') || '');
          formData.set('mobile', sanitizedMobile);

          if (!sanitizedMobile) {
              setTeacherMobileError(true);
              Swal.fire({
                  icon: 'error',
                  title: 'Invalid mobile number',
                  text: 'Must provide numbers only.'
              });
              teacherMobileInput.focus();
              return;
          }

          setTeacherMobileError(false);

          if (tid) {
              formData.append('_method', 'PUT');
          }
          const apiUrl = tid ? `{{ url('/api/teachers') }}/${tid}` : '{{ url('/api/teachers') }}';
          axios.post(apiUrl, formData, {
                  headers: {
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'Content-Type': 'multipart/form-data'
                  }
              })
              .then((res) => {
                  Toastify({
                      text: "Teacher Saved Successfully!",
                      gravity: "top",
                      position: "right",
                      style: {
                          background: "#10b981"
                      }
                  }).showToast();
                  teacherModal.classList.add('hidden');
                  
                  if (document.getElementById('teacherTableBody')) {
                      fetchTeachers(currentPage);
                  } else {
                      const resData = res.data && res.data.data ? res.data.data : (res.data || null);
                      const newTeacher = resData && resData.teacher ? resData.teacher : resData;
                      const handleModalRestore = () => {
                          if (window.lastActiveModalId) {
                              document.getElementById(window.lastActiveModalId)?.classList.remove('hidden');
                              if (window.lastActiveModalId === 'permModal' && newTeacher && newTeacher.id) {
                                  if (typeof setSelectedValue === 'function') {
                                      setSelectedValue('teacher_id', newTeacher.id);
                                  }
                              }
                              window.lastActiveModalId = null;
                          }
                      };

                      if (typeof initData === 'function') {
                          const resInit = initData();
                          if (resInit && typeof resInit.then === 'function') {
                              resInit.then(handleModalRestore);
                          } else {
                              handleModalRestore();
                          }
                      } else {
                          handleModalRestore();
                      }
                  }
              }).catch(err => {
                  let errorMsg = 'Action failed';
                  if (err.response && err.response.data.errors) {
                      errorMsg = Object.values(err.response.data.errors).flat().join('\n');
                  }
                  Swal.fire({
                      icon: 'error',
                      title: 'Submission Failed',
                      text: errorMsg
                  });
              });
      });

      function editTeacher(id) {
          axios.get('{{ url('/api/teachers') }}/' + id).then(res => {
              const t = res.data;
              document.getElementById('teacher_id').value = t.id;
              document.querySelector('input[name="name"]').value = t.name;
              document.querySelector('input[name="designation"]').value = t.designation;
              document.querySelector('input[name="mobile"]').value = t.mobile;
              document.querySelector('input[name="email"]').value = t.email;
              const dobInput = document.querySelector('input[name="dob"]');
              if (dobInput) dobInput.value = t.dob;
              const photoUrl = t.photo ? `/storage/${t.photo}` :
                  'https://ui-avatars.com/api/?background=random&name=' + t.name;
              imagePreview.innerHTML = `<img src="${photoUrl}" />`;
              teacherModal.classList.remove('hidden');
          });
      }

      function deleteTeacher(id) {
          Swal.fire({
              title: 'Delete Faculty?',
              text: "This action cannot be undone.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#000000',
              cancelButtonColor: '#64748b',
              confirmButtonText: 'CONFIRM DELETE',
              customClass: {
                  popup: 'modal-content-sharp'
              }
          }).then((result) => {
              if (result.isConfirmed) {
                  axios.delete('{{ url('/api/teachers') }}/' + id, {
                      headers: {
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      }
                  }).then(() => {
                      Toastify({
                          text: "Record Deleted",
                          style: {
                              background: "#ef4444"
                          }
                      }).showToast();
                      fetchTeachers(currentPage);
                  });
              }
          });
      }
      document.addEventListener('DOMContentLoaded', function() {

          const toggleModal = (id, show) => {
              document.getElementById(id).classList.toggle('hidden', !show);
          };
          const resetDesignationFilter = () => {
              const input = document.getElementById('teacherFilter');
              const label = document.querySelector('#teacherFilterButton [data-dropdown-select-label]');
              const designation = document.getElementById('teacherFilterDesignation');

              if (input) {
                  input.value = '';
              }

              if (label) {
                  label.textContent = label.dataset.placeholder || 'Teacher';
              }

              if (designation) {
                  designation.value = '';
              }
          };

          document.querySelectorAll('#teacherFilterMenu [data-dropdown-select-option]').forEach((option) => {
              option.addEventListener('click', () => {
                  const designation = document.getElementById('teacherFilterDesignation');

                  if (designation) {
                      designation.value = option.dataset.optionDesignation || '';
                  }
              });

              if (option.getAttribute('aria-selected') === 'true') {
                  const designation = document.getElementById('teacherFilterDesignation');

                  if (designation) {
                      designation.value = option.dataset.optionDesignation || '';
                  }
              }
          });

          if (!window.location.pathname.includes('class-permission')) {
              document.getElementById('btnFilter')?.addEventListener('click', () => toggleModal('filterModal', true));
              document.getElementById('teacherFilterForm')?.addEventListener('submit', (event) => {
                  event.preventDefault();
                  fetchTeachers(1);
              });
              document.getElementById('resetFilter')?.addEventListener('click', () => {
                  resetDesignationFilter();
                  toggleModal('filterModal', false);
                  fetchTeachers(1);
              });
              document.getElementById('applyFilter')?.addEventListener('click', () => {
                  toggleModal('filterModal', false);
                  fetchTeachers(1);
              });
              document.getElementById('btnExport')?.addEventListener('click', () => toggleModal('exportModal', true));
              document.getElementById('closeExport')?.addEventListener('click', () => toggleModal('exportModal', false));
          }
          window.onclick = function(event) {
              if (event.target.classList.contains('premium-modal')) {
                  event.target.classList.add('hidden');
              }
          };

          // Replacement photo preview
          const replacementPhotoInput = document.getElementById('replacementPhotoInput');
          const replacementImagePreview = document.getElementById('replacementImagePreview');
          if (replacementPhotoInput && replacementImagePreview) {
              replacementPhotoInput.addEventListener('change', function() {
                  const file = this.files[0];
                  if (file) {
                      const reader = new FileReader();
                      reader.onload = (e) => replacementImagePreview.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" />`;
                      reader.readAsDataURL(file);
                  } else {
                      replacementImagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
                  }
              });
          }

          // Replacement mobile input validation
          const replacementMobileInput = document.getElementById('replacementMobile');
          const replacementMobileError = document.getElementById('replacementMobileError');
          if (replacementMobileInput) {
              replacementMobileInput.addEventListener('input', function() {
                  const sanitizedValue = sanitizeEnglishDigits(this.value);
                  const hasInvalidChars = this.value !== sanitizedValue;
                  this.value = sanitizedValue;
                  if (replacementMobileError) {
                      replacementMobileError.classList.toggle('hidden', !hasInvalidChars);
                  }
              });
          }

          // Deactivate & Replace form submit
          const deactivateTeacherForm = document.getElementById('deactivateTeacherForm');
          if (deactivateTeacherForm) {
              deactivateTeacherForm.addEventListener('submit', function(e) {
                  e.preventDefault();
                  const oldId = document.getElementById('deactivate_old_teacher_id').value;
                  const formData = new FormData(this);
                  const sanitizedMobile = sanitizeEnglishDigits(formData.get('mobile') || '');
                  formData.set('mobile', sanitizedMobile);

                  if (!sanitizedMobile) {
                      if (replacementMobileError) {
                          replacementMobileError.classList.remove('hidden');
                      }
                      Swal.fire({
                          icon: 'error',
                          title: 'Invalid mobile number',
                          text: 'Must provide numbers only.'
                      });
                      replacementMobileInput?.focus();
                      return;
                  }

                  if (replacementMobileError) {
                      replacementMobileError.classList.add('hidden');
                  }

                  const apiUrl = `{{ url('/api/teachers') }}/${oldId}/deactivate`;
                  axios.post(apiUrl, formData, {
                          headers: {
                              'X-CSRF-TOKEN': '{{ csrf_token() }}',
                              'Content-Type': 'multipart/form-data'
                          }
                      })
                      .then((res) => {
                          Toastify({
                              text: "Teacher deactivated and replaced successfully!",
                              gravity: "top",
                              position: "right",
                              style: {
                                  background: "#10b981"
                              }
                          }).showToast();
                          document.getElementById('deactivateTeacherModal').classList.add('hidden');
                          deactivateTeacherForm.reset();
                          if (replacementImagePreview) {
                              replacementImagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
                          }
                          fetchTeachers(currentPage);
                      }).catch(err => {
                          let errorMsg = 'Action failed';
                          if (err.response && err.response.data.errors) {
                              errorMsg = Object.values(err.response.data.errors).flat().join('\n');
                          } else if (err.response && err.response.data.message) {
                              errorMsg = err.response.data.message;
                          }
                          Swal.fire({
                              icon: 'error',
                              title: 'Submission Failed',
                              text: errorMsg
                          });
                      });
              });
          }

          // Close deactivation modal
          document.getElementById('closeDeactivateTeacherModal')?.addEventListener('click', () => {
              document.getElementById('deactivateTeacherModal').classList.add('hidden');
          });
      });

      // Toggle Teacher Status handler
      function toggleTeacherStatus(id, currentStatus, name, designation, idNumber) {
          if (currentStatus === 'Hold') {
              // Activate Teacher flow
              Swal.fire({
                  title: 'Reactivate Faculty?',
                  text: `Are you sure you want to activate ${name}?`,
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonColor: '#10b981',
                  cancelButtonColor: '#64748b',
                  confirmButtonText: 'ACTIVATE',
                  customClass: {
                      popup: 'modal-content-sharp'
                  }
              }).then((result) => {
                  if (result.isConfirmed) {
                      axios.post(`{{ url('/api/teachers') }}/${id}/activate`, {}, {
                          headers: {
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          }
                      }).then((res) => {
                          Toastify({
                              text: "Teacher activated successfully!",
                              gravity: "top",
                              position: "right",
                              style: {
                                  background: "#10b981"
                              }
                          }).showToast();
                          fetchTeachers(currentPage);
                      }).catch(err => {
                          let errorMsg = 'Failed to activate teacher';
                          if (err.response && err.response.data.message) {
                              errorMsg = err.response.data.message;
                          }
                          Swal.fire({
                              icon: 'error',
                              title: 'Action Failed',
                              text: errorMsg
                          });
                      });
                  }
              });
          } else {
              // Deactivate & Replace flow
              document.getElementById('deactivate_old_teacher_id').value = id;
              document.getElementById('deactivate_teacher_name').textContent = name;
              document.getElementById('deactivate_teacher_designation').textContent = designation || 'N/A';
              document.getElementById('deactivate_teacher_id_number').textContent = idNumber || 'PENDING';
              document.getElementById('deactivateTeacherModal').classList.remove('hidden');
          }
      }
  </script>

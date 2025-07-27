var telInput = $("#mobile"),
    errorMsg = $("#error-msg"),
    validMsg = $("#valid-msg");

console.log("orchid")

if($("#mobile")[0] != undefined) {
    // Initialise plugin
    telInput.intlTelInput({
        allowExtensions: true,
        formatOnDisplay: true,
        autoFormat: true,
        autoHideDialCode: true,
        autoPlaceholder: true,
        nationalMode: false,
        numberType: "MOBILE",
        preferredCountries: ['ru', 'ae', 'qa', 'om', 'bh', 'kw', 'ma'],
        preventInvalidNumbers: true,
        separateDialCode: false,
        initialCountry: "ru", // Set initial country to Russia
        geoIpLookup: function(callback) {
            $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : "ru"; // Default to Russia if geoIpLookup fails
                callback(countryCode);
            });
        },
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/11.0.9/js/utils.js"
    });

    var reset = function() {
        telInput.removeClass("error");
        errorMsg.addClass("hide");
        validMsg.addClass("hide");
    };

    // On blur: validate
    telInput.blur(function() {
        reset();
        if ($.trim(telInput.val())) {
            if (telInput.intlTelInput("isValidNumber")) {
                validMsg.removeClass("hide");
            } else {
                telInput.addClass("error");
                errorMsg.removeClass("hide");
            }
        }
    });

    // On keyup / change flag: reset
    telInput.on("keyup change", reset);

    // Manually trigger the initialization to ensure the country is set to Russia
    telInput.intlTelInput("setCountry", "ru");
}



var swiper = new Swiper(".mySwiper", {
    slidesPerView: 4,
    spaceBetween: 30,
    centeredSlides: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        // when window width is >= 480px
        480: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
          slideToClickedSlide: true,
          },
        // when window width is >= 640px
        1000: {
            slidesPerView: 3,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
    }
  });


  var swiper = new Swiper(".mySwiper1", {
    slidesPerView: 4,
    spaceBetween: 30,
    centeredSlides: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        // when window width is >= 480px
        480: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
          slideToClickedSlide: true,
          },
        // when window width is >= 640px
        1000: {
            slidesPerView: 3,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
    }
  });

  var swiper = new Swiper(".mySwiper3", {
    slidesPerView: 4,
    spaceBetween: 30,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        // when window width is >= 480px
        480: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 20,
          slideToClickedSlide: true,
          },
        // when window width is >= 640px
        1000: {
            slidesPerView: 3,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
        1200: {
            slidesPerView: 4,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
    }
  });

  var swiper = new Swiper(".mySwiper4", {
    slidesPerView: 4,
    spaceBetween: 30,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
        // when window width is >= 320px
        320: {
            slidesPerView: 1,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        // when window width is >= 480px
        480: {
            slidesPerView: 2,
            spaceBetween: 20,
            slideToClickedSlide: true,
        },
        768: {
          slidesPerView: 3,
          spaceBetween: 20,
          slideToClickedSlide: true,
          },
        // when window width is >= 640px
        1000: {
            slidesPerView: 4,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
        1200: {
            slidesPerView: 5,
            spaceBetween: 16,
            slideToClickedSlide: true,
        },
    }
  });


  document.querySelectorAll('.pagination a').forEach(item => {
    item.addEventListener('click', event => {
        document.querySelector('.pagination a.active').classList.remove('active');
        event.target.classList.add('active');
    });
});

let fileList = [], fileLimit = 5;

function handleFiles(files) {
    const fileInput = document.getElementById('fileInput');
    const fileListContainer = document.getElementById('fileList');

    for (let i = 0; i < files.length; i++) {
        if (fileList.length >= fileLimit) {
            alert('Можно добавить до '+fileLimit+' файлов');
            break;
        }

        const file = files[i];
        const fileSize = (file.size / 1024).toFixed(2); // Размер в килобайтах
        const fileName = file.name;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-list-item';
        fileItem.innerHTML = `
            <span>${fileName} (${fileSize} Кбайт)</span>
            <button onclick="removeFile(${fileList.length})">X</button>
        `;

        fileList.push(file);
        fileInput.files[i] = file;
        fileListContainer.appendChild(fileItem);
    }

    // Clear the file input
    fileInput.value = '';
}

function uploadLimit(count) {
    fileLimit = count || 5
}

function removeFile(index) {
    fileList.splice(index, 1);
    updateFileList();
}

function updateFileList() {
    const fileListContainer = document.getElementById('fileList');
    fileListContainer.innerHTML = '';

    fileList.forEach((file, index) => {
        const fileSize = (file.size / 1024).toFixed(2); // Размер в килобайтах
        const fileName = file.name;

        const fileItem = document.createElement('div');
        fileItem.className = 'file-list-item';
        fileItem.innerHTML = `
            <span>${fileName} (${fileSize} Кбайт)</span>
            <button onclick="removeFile(${index})">X</button>
        `;

        fileListContainer.appendChild(fileItem);
    });
}

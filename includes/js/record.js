       
  const recordButton = document.getElementById('recordButton');
  const statusDiv = document.getElementById('status');
  const timerDiv = document.getElementById('timer');
  const loadingBar = document.getElementById('loadingBar');
  const progressBar = document.getElementById('progress');
  const classInput = document.getElementById('class');
  const titleInput = document.getElementById('title');
  const chapterInput = document.getElementById('chapter');
  const alertDiv = document.getElementById('alert');
  let mediaRecorder;
  let audioChunks = [];
  let startTime;
  let timerInterval;
  
  
  recordButton.addEventListener('click', async () => {
      // Check if class, title, or chapter are empty
      if (!classInput.value || !titleInput.value || !chapterInput.value) {
          alertDiv.textContent = "Please fill in all fields (Class, Title, Chapter) before starting the recording.";
          alertDiv.style.display = 'block';
          return; // Don't proceed with recording if fields are not filled would mess up SQL
      }
      // If fields are filled, hide the alert
      alertDiv.style.display = 'none';
      if (!mediaRecorder || mediaRecorder.state === 'inactive') {
          const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
          mediaRecorder = new MediaRecorder(stream);
          mediaRecorder.ondataavailable = event => audioChunks.push(event.data);
          mediaRecorder.onstop = async () => {
              clearInterval(timerInterval);
              const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
              const formData = new FormData(document.getElementById('audioForm'));
              formData.append('audioBlob', audioBlob);
  
              statusDiv.textContent = 'Uploading and transcribing...';
              loadingBar.style.display = 'flex'; // Show loading bar
              progressBar.style.width = '0%';  // Reset progress bar
  
    
              let progress = 0;
              const interval = setInterval(() => {
                  if (progress < 100) {
                      progress += 10;
                      progressBar.style.width = `${progress}%`;
                  } else {
                      clearInterval(interval);
                  }
              }, 500);
              // function to refresh to show check
              const response = await fetch('', { method: 'POST', body: formData });
              const result = await response.text();
              statusDiv.innerHTML = result;
              loadingBar.style.display = 'none';
              location.reload(); 
          };
          audioChunks = [];
          mediaRecorder.start();
          startTime = Date.now();
          timerInterval = setInterval(updateTimer, 1000);
  
          recordButton.textContent = 'Stop Recording';
          recordButton.classList.add('recording');
          statusDiv.textContent = 'Recording...';
      } else if (mediaRecorder.state === 'recording') {
          mediaRecorder.stop();
          recordButton.textContent = 'Start Recording';
          recordButton.classList.remove('recording');
      }
  });
  // some random math for the timer from huggingface
  function updateTimer() {
      const elapsed = Math.floor((Date.now() - startTime) / 1000);
      const minutes = String(Math.floor(elapsed / 60)).padStart(2, '0');
      const seconds = String(elapsed % 60).padStart(2, '0');
      timerDiv.textContent = `Recording Duration: ${minutes}:${seconds}`;
  }
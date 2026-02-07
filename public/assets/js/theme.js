function updateLiveTime() {
  const formattedTime = moment().format("DD MMM YYYY h:mm:ss A");
  document.getElementById("ic-live-date-time").textContent = formattedTime;
}
jQuery(function($){
    $(document).ready(function() {

        updateLiveTime();
        setInterval(updateLiveTime, 1000);
        $('.select2').select2();
        $('.flatpickr-range').flatpickr({
            mode: 'range'
        });
        $('.flatpickr-single').flatpickr();
        
    });
});
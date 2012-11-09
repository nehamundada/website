module EventDescription
  def isOldEvent(pEeventDate)
   require 'date';
    eventDate = Date.parse(pEeventDate.strftime('%Y/%m/%d'))
    todaysDate = Date.today;
    return eventDate < todaysDate;
  end
end

Liquid::Template.register_filter(EventDescription)
package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.ContactVO;
	import org.aerial.bootstrap.Aerial;

	public class ContactService extends AbstractService
	{
		public function ContactService()
		{
			super("ContactService", Aerial.SERVER_URL, ContactVO);
		}
	}
}
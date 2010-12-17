package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.PhonenumberVO;
	import org.aerial.bootstrap.Aerial;

	public class PhonenumberService extends AbstractService
	{
		public function PhonenumberService()
		{
			super("PhonenumberService", Aerial.SERVER_URL, PhonenumberVO);
		}
	}
}
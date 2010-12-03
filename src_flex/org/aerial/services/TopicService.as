package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.TopicVO;
	import org.aerial.bootstrap.Aerial;

	public class TopicService extends AbstractService
	{
		public function TopicService()
		{
			super("TopicService", Aerial.SERVER_URL, TopicVO);
		}
	}
}
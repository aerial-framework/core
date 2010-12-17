package org.aerial.services
{
	import org.aerial.rpc.AbstractService;
	
	import org.aerial.vo.GroupUserVO;
	import org.aerial.bootstrap.Aerial;

	public class GroupUserService extends AbstractService
	{
		public function GroupUserService()
		{
			super("GroupUserService", Aerial.SERVER_URL, GroupUserVO);
		}
	}
}